# MoneyFlow - Personal Finance Management Prototype

## Navigation Map (User Flow)

The application follows a circular flow typical for finance tools:

1. **Home (Landing/Blog)**: Introduction, benefits, and articles about saving.
2. **Login/Register**: Secure access.
3. **Dashboard (Main Panel)**: Summary of balance, monthly expense chart, latest transactions.
4. **Transactions List (Detail View)**: Filters by category (salary, food, leisure) and dates.
5. **User Profile**: Settings for currency, monthly budget, personal data.

## Wireframe Description

The homepage is clean and combines educational content with app access.

- **Header**: Logo ("MoneyFlow"), links (Blog, Tutorials), highlighted "My Account" button.
- **Hero Section**: Powerful title like "Take control of your money" and an interface image.
- **Post List (Grid)**: Cards with finance tips (e.g., "How to save 20% of your salary").
- **Sidebar**: Article search and expense categories.
- **Footer**: Legal links and social media.

## Identity Visual

- **Colors**: Emerald Green (growth), Navy Blue (seriousness), Light Gray (neutral for data).
- **Typography**: Inter or Roboto (highly readable for numbers and tables).

## MVC Correspondence

In the future MVC structure:

- This static HTML will become PHP views in `/views`.
- Post list will be a `foreach ($posts as $post)` from PostController querying Post model.
- Login form will send data to UserController for verification against `users` table and session start.