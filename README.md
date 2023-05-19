# Xp Arena - Readme

<p align="center">
  <img src="storage/app/public/logo/LogoXpArena.png" alt="Xp Arena Logo">
</p>

<p align="center">
  <a href="https://packagist.org/packages/laravel/framework">
    <img src="https://img.shields.io/packagist/l/laravel/framework" alt="License">
  </a>
</p>

XP Arena is a Laravel-Vue application that allows users to create and share games. The primary purpose of the app is to motivate and engage users in completing tasks by rewarding them with experience points (XP). Users who follow these games can complete the assigned tasks and earn experience points, enabling them to level up. This gamification approach aims to make mundane or intimidating tasks more exciting and rewarding.

## Features

XP Arena offers the following features:

    - User Registration and Authentication: Users can create accounts and log in to access the application's functionalities.

    - Game Creation: Users can create their own games by defining a set of tasks or challenges. Each game will have a unique name and description.

    - Task Management: Game creators can add tasks to their games. Tasks can be defined with a title, description, and XP reward. They can also be marked as completed by the game creator.

    - Game Following: Users can choose to follow games created by other users. By following a game, users can view the tasks associated with it and mark them as completed.

    - XP and Leveling System: Users earn experience points (XP) by completing tasks within the games they follow. Accumulating a certain amount of XP will allow users to level up. Leveling up provides a sense of achievement and progression within the app.

## Installation

To run XP Arena locally, please follow these steps:

    - Clone the repository: git clone https://github.com/Dejorden94/Xp-arena.git
    - Navigate to the project directory: cd xp-arena
    - Install the dependencies: composer install (for Laravel) and npm install (for Vue)
    - Rename the .env.example file to .env and update the necessary configuration settings, such as the database connection details.
    - Generate an application key: php artisan key:generate
    - Run the database migrations: php artisan migrate
    - Compile the frontend assets: npm run dev (for development) or npm run production (for production)
    - Start the development server: php artisan serve
    - Access the application in your browser at http://localhost:8000

Please note that these instructions assume you have PHP, Composer, and Node.js installed on your machine.
Contributing

Contributions to XP Arena are welcome! If you encounter any issues or have suggestions for improvements, please open an issue on the GitHub repository.

## License

XP Arena is open-source software licensed under the MIT license.
Acknowledgements

XP Arena was developed using the Laravel PHP framework and Vue.js JavaScript framework. We would like to thank the Laravel and Vue.js communities for their excellent tools and resources.
Contact

If you have any questions or need assistance, feel free to contact me at dejordenm@gmail.com.