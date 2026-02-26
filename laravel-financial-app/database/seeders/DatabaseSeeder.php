<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Post;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create admin user
        User::create([
            'name' => 'Administrador',
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);

        // Create demo user
        User::create([
            'name' => 'Usuario Demo',
            'email' => 'demo@example.com',
            'password' => bcrypt('password'),
            'role' => 'user',
        ]);

        // Create sample blog posts
        Post::create([
            'title' => 'Bienvenido al Blog Financiero',
            'content' => 'Este es un artículo de ejemplo sobre gestión financiera personal. Aprende a controlar tus gastos, ahorrar efectivamente y invertir de manera inteligente para asegurar tu futuro económico.',
            'summary' => 'Introducción al blog financiero y consejos básicos.',
            'category' => 'Finanzas',
            'tags' => 'bienvenida,finanzas,ahorro',
            'image' => 'https://picsum.photos/seed/finance1/800/400.jpg',
            'author_id' => 1,
        ]);

        Post::create([
            'title' => 'Cómo Empezar a Invertir',
            'content' => 'La inversión es una herramienta fundamental para hacer crecer tu dinero. Aprende los conceptos básicos, diferentes tipos de inversiones y cómo construir una cartera diversificada.',
            'summary' => 'Guía básica para principiantes en inversiones.',
            'category' => 'Inversiones',
            'tags' => 'inversión,bolsa,principiantes',
            'image' => 'https://picsum.photos/seed/investment1/800/400.jpg',
            'author_id' => 1,
        ]);

        Post::create([
            'title' => 'Presupuesto Mensual: Tu Primer Paso',
            'content' => 'Crear un presupuesto mensual es el primer paso hacia la estabilidad financiera. Aprende a seguir el método 50/30/20, a categorizar tus gastos y a mantener el control de tu dinero.',
            'summary' => 'Aprende a crear tu primer presupuesto mensual.',
            'category' => 'Ahorro',
            'tags' => 'presupuesto,ahorro,planificación',
            'image' => 'https://picsum.photos/seed/budget1/800/400.jpg',
            'author_id' => 1,
        ]);

        Post::create([
            'title' => 'Inversiones en Acciones: Guía para Principiantes',
            'content' => 'Las acciones representan una parte de la propiedad de una empresa. Aprende a analizar acciones, a diversificar tu cartera y a manejar el riesgo en el mercado de valores.',
            'summary' => 'Guía básica para invertir en el mercado de valores.',
            'category' => 'Inversiones',
            'tags' => 'acciones,bolsa,valoraciones',
            'image' => 'https://picsum.photos/seed/stocks1/800/400.jpg',
            'author_id' => 1,
        ]);

        Post::create([
            'title' => 'Técnicas de Ahorro para Principiantes',
            'content' => 'Aprende técnicas efectivas para ahorrar dinero, como la automatización de ahorros, el seguimiento de gastos y la eliminación de gastos innecesarios.',
            'summary' => 'Descubre estrategias para ahorrar más cada mes.',
            'category' => 'Ahorro',
            'tags' => 'ahorro,ahorro-automático,control-gastos',
            'image' => 'https://picsum.photos/seed/saving1/800/400.jpg',
            'author_id' => 1,
        ]);
    }
}
