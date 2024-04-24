
# Laravel Travel Agency API

This project is a Laravel APIs application developed for a travel agency. It provides a set of endpoints for managing users, travels, and tours, catering to both administrative and public needs.

## Key Features

- **User Management**: Includes endpoints for creating new users, with roles assigned to differentiate between admin and editor roles.
- **Travel Management**: Provides endpoints for creating and updating travel details, including name, description, and tour information.
- **Tour Management**: Allows for the creation of tours within a travel, specifying starting date, ending date, price, and additional notes.
- **Public Endpoints**: Offers public endpoints for accessing a list of paginated travels and tours, with options for filtering by price, date range, and sorting options.
- **Data Formatting**: Handles formatting of tour prices for display, dividing by 100 and returning as formatted currency.
- **Testing**: Includes feature tests covering functionality of all endpoints, ensuring thorough testing of input validation, authentication, authorization, and response formats.


## Installation

1. **Clone the Repository**:
   ```bash
   git clone https://github.com/yourusername/your-repo.git](https://github.com/AwsAboud/travel-api.git
2. **Install Dependencies**:
    ```bash
   comoposer install
    
3. **Set up environment**:
- Copy `.env.example` to `.env` and configure your database settings.

- Generate application key:
     ```bash
      php artisan key:generate

4. **Migrate Database**:
   ```bash
   php artisan migrate
5. **Start Server**:
   ```bash
   php artisan serve
