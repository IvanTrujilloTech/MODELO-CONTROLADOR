<?php
// modelo de posts con funcionalidad rag
// aqui esta la busqueda semantica y sugerencias
class post {
    private $conn;
    private $table_name = "posts";

    public $id;
    public $titulo;
    public $contenido;
    public $resumen;
    public $categoria;
    public $tags;
    public $imagen;
    public $autor_id;
    public $created_at;
    public $updated_at;

    public function __construct($db) {
        $this->conn = $db;
    }

    // crea un nuevo post
    public function create() {
        $query = "insert into " . $this->table_name . " 
                  set titulo=:titulo, contenido=:contenido, resumen=:resumen, 
                      categoria=:categoria, tags=:tags, imagen=:imagen, autor_id=:autor_id";

        $stmt = $this->conn->prepare($query);

        // limpiamos input
        $this->titulo = security::sanitize_string($this->titulo);
        $this->contenido = security::sanitize_string($this->contenido);
        $this->resumen = security::sanitize_string($this->resumen ?? '');
        $this->categoria = security::sanitize_string($this->categoria);
        $this->tags = security::sanitize_string($this->tags ?? '');
        $this->imagen = security::sanitize_string($this->imagen ?? '');

        // bind valores
        $stmt->bindparam(":titulo", $this->titulo);
        $stmt->bindparam(":contenido", $this->contenido);
        $stmt->bindparam(":resumen", $this->resumen);
        $stmt->bindparam(":categoria", $this->categoria);
        $stmt->bindparam(":tags", $this->tags);
        $stmt->bindparam(":imagen", $this->imagen);
        $stmt->bindparam(":autor_id", $this->autor_id, pdo::param_int);

        if ($stmt->execute()) {
            $this->id = $this->conn->lastinsertid();
            return true;
        }
        return false;
    }

    // cogemos un post por id
    public function getbyid($id) {
        $query = "select * from " . $this->table_name . " where id = ? limit 0,1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindparam(1, $id, pdo::param_int);
        $stmt->execute();
        
        $row = $stmt->fetch(pdo::fetch_assoc);
        if ($row) {
            $this->id = $row['id'];
            $this->titulo = $row['titulo'];
            $this->contenido = $row['contenido'];
            $this->resumen = $row['resumen'];
            $this->categoria = $row['categoria'];
            $this->tags = $row['tags'];
            $this->imagen = $row['imagen'];
            $this->autor_id = $row['autor_id'];
            $this->created_at = $row['created_at'];
            $this->updated_at = $row['updated_at'];
            return true;
        }
        return false;
    }

    // cogemos todos los posts
    public function getall($limit = 10, $offset = 0) {
        $query = "select * from " . $this->table_name . " order by created_at desc limit ? offset ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindvalue(1, $limit, pdo::param_int);
        $stmt->bindvalue(2, $offset, pdo::param_int);
        $stmt->execute();
        return $stmt;
    }

    // cogemos posts por categoria
    public function getbycategory($categoria, $limit = 5) {
        $query = "select * from " . $this->table_name . " where categoria = ? order by created_at desc limit ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindparam(1, $categoria);
        $stmt->bindvalue(2, $limit, pdo::param_int);
        $stmt->execute();
        return $stmt;
    }

    // funcion rag: buscar posts relacionados
    public function findrelatedposts($post_id, $keywords = [], $limit = 3) {
        $relatedposts = [];
        
        if (empty($keywords) && !$this->getbyid($post_id)) {
            return $relatedposts;
        }

        // construimos la consulta de busqueda
        $conditions = ["id != ?"];
        $params = [$post_id];
        
        // anadimos coincidencia de keywords
        if (!empty($keywords)) {
            $keywordconditions = [];
            foreach ($keywords as $index => $keyword) {
                $keyword = security::sanitize_string($keyword);
                if (strlen($keyword) > 2) {
                    $keywordconditions[] = "(titulo like ? or contenido like ? or tags like ? or categoria like ?)";
                    $searchterm = "%{$keyword}%";
                    $params[] = $searchterm;
                    $params[] = $searchterm;
                    $params[] = $searchterm;
                    $params[] = $searchterm;
                }
            }
            if (!empty($keywordconditions)) {
                $conditions[] = "(" . implode(" or ", $keywordconditions) . ")";
            }
        }

        // anadimos coincidencia de categoria si existe
        if (!empty($this->categoria)) {
            $conditions[] = "categoria = ?";
            $params[] = $this->categoria;
        }

        // construimos la consulta final
        $whereclause = implode(" and ", $conditions);
        $query = "select * from " . $this->table_name . " where {$whereclause} order by created_at desc limit " . (int)$limit;
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute($params);
        
        while ($row = $stmt->fetch(pdo::fetch_assoc)) {
            $score = $this->calculaterelevancescore($row, $keywords);
            $row['relevance_score'] = $score;
            $relatedposts[] = $row;
        }
        
        usort($relatedposts, function($a, $b) {
            return $b['relevance_score'] - $a['relevance_score'];
        });
        
        return $relatedposts;
    }

    // calcular puntuacion de relevancia
    private function calculaterelevancescore($post, $keywords) {
        $score = 0;
        $title = strtolower($post['titulo'] ?? '');
        $content = strtolower($post['contenido'] ?? '');
        $tags = strtolower($post['tags'] ?? '');
        $categoria = strtolower($post['categoria'] ?? '');
        
        foreach ($keywords as $keyword) {
            $keyword = strtolower(trim($keyword));
            if (strlen($keyword) < 3) continue;
            
            if (strpos($title, $keyword) !== false) {
                $score += 10;
            }
            elseif (strpos($tags, $keyword) !== false) {
                $score += 5;
            }
            elseif (strpos($content, $keyword) !== false) {
                $score += 2;
            }
        }
        
        if (!empty($this->categoria) && $categoria === strtolower($this->categoria)) {
            $score += 3;
        }
        
        $created = strtotime($post['created_at']);
        $age = time() - $created;
        $daysold = $age / 86400;
        if ($daysold < 7) {
            $score += 2;
        } elseif ($daysold < 30) {
            $score += 1;
        }
        
        return $score;
    }

    // extraer keywords del contenido
    public function extractkeywords($content, $limit = 5) {
        $stopwords = [
            'el', 'la', 'los', 'las', 'un', 'una', 'unos', 'unas', 'de', 'del', 'al',
            'en', 'por', 'para', 'con', 'sin', 'sobre', 'entre', 'y', 'e', 'o', 'u',
            'que', 'cual', 'cuales', 'como', 'cuando', 'donde', 'quien', 'es', 'son',
            'este', 'esta', 'estos', 'estas', 'ese', 'esa', 'esos', 'esas', 'aquel',
            'aquella', 'aquellos', 'aquellas', 'lo', 'le', 'les', 'se', 'si', 'no',
            'pero', 'mas', 'muy', 'ya', 'todo', 'todos', 'toda', 'todas', 'mi', 'tu',
            'su', 'mis', 'tus', 'sus', 'nuestro', 'nuestra', 'nuestros', 'nuestras',
            'your', 'the', 'and', 'or', 'but', 'is', 'are', 'was', 'were', 'be', 'been',
            'being', 'have', 'has', 'had', 'do', 'does', 'did', 'will', 'would', 'could'
        ];
        
        $content = strtolower(strip_tags($content));
        $content = preg_replace('/[^\p{l}\p{n}\s]/u', ' ', $content);
        $words = preg_split('/\s+/', $content);
        
        $wordcount = [];
        foreach ($words as $word) {
            $word = trim($word);
            if (strlen($word) > 2 && !in_array($word, $stopwords) && is_numeric($word) === false) {
                $wordcount[$word] = ($wordcount[$word] ?? 0) + 1;
            }
        }
        
        arsort($wordcount);
        
        return array_slice(array_keys($wordcount), 0, $limit);
    }

    // posts sugeridos basados en actividad reciente
    public function getsuggestedposts($user_id = null, $limit = 5) {
        $query = "select * from " . $this->table_name . " order by created_at desc limit ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindvalue(1, $limit, pdo::param_int);
        $stmt->execute();
        
        return $stmt->fetchall(pdo::fetch_assoc);
    }

    // buscar posts por query
    public function search($query, $limit = 10) {
        $searchterm = "%" . security::sanitize_string($query) . "%";
        $sql = "select * from " . $this->table_name . " 
                where titulo like ? or contenido like ? or resumen like ? or tags like ?
                order by created_at desc limit ?";
        
        $stmt = $this->conn->prepare($sql);
        $stmt->bindparam(1, $searchterm);
        $stmt->bindparam(2, $searchterm);
        $stmt->bindparam(3, $searchterm);
        $stmt->bindparam(4, $searchterm);
        $stmt->bindvalue(5, $limit, pdo::param_int);
        $stmt->execute();
        
        return $stmt;
    }

    // actualiza un post
    public function update() {
        $query = "update " . $this->table_name . " 
                  set titulo=:titulo, contenido=:contenido, resumen=:resumen, 
                      categoria=:categoria, tags=:tags, imagen=:imagen, updated_at=now()
                  where id=:id";

        $stmt = $this->conn->prepare($query);

        $this->titulo = security::sanitize_string($this->titulo);
        $this->contenido = security::sanitize_string($this->contenido);
        $this->resumen = security::sanitize_string($this->resumen ?? '');
        $this->categoria = security::sanitize_string($this->categoria);
        $this->tags = security::sanitize_string($this->tags ?? '');
        $this->imagen = security::sanitize_string($this->imagen ?? '');

        $stmt->bindparam(":titulo", $this->titulo);
        $stmt->bindparam(":contenido", $this->contenido);
        $stmt->bindparam(":resumen", $this->resumen);
        $stmt->bindparam(":categoria", $this->categoria);
        $stmt->bindparam(":tags", $this->tags);
        $stmt->bindparam(":imagen", $this->imagen);
        $stmt->bindparam(":id", $this->id, pdo::param_int);

        return $stmt->execute();
    }

    // borra un post
    public function delete() {
        $query = "delete from " . $this->table_name . " where id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindparam(1, $this->id, pdo::param_int);
        return $stmt->execute();
    }

    // cuenta posts por categoria
    public function getcountbycategory() {
        $query = "select categoria, count(*) as count from " . $this->table_name . " group by categoria";
        $stmt = $this->conn->query($query);
        return $stmt->fetchall(pdo::fetch_assoc);
    }
}
