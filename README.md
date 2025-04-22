<p align="center"><a href="https://peira.space" target="_blank"><img src="https://www.peira.space/img/peira.svg" width="400" alt="Laravel Logo"></a></p>

# Peira Project

The Peira Project is an art and cultural initiative that fosters collaboration and critical practice. It offers training and workshops for artists, focusing on concept development, cultural reflection, and funding strategies. The project also produces podcasts and media works exploring themes like migrant resistance and cultural identity.

The website, built with Laravel, serves as a digital home for these initiatives — presenting content such as podcast episodes, documentation projects, event series, and artistic statements. It functions as a hybrid between an archive and a living program, with a clean and multilingual frontend powered by a headless Drupal CMS.

## Features

- Dynamic content loading from a Drupal API (projects, quotes, series, episodes, and news)
- Localized content support (German and English)
- Laravel Blade templates with custom data models
- Responsive layout and accessible UI for mobile and desktop
- Newsletter subscription integration
- CMS-driven header and footer navigation
- Image and video handling with fallback metadata
- Smart conditional rendering of date fields and tag filters

## Tech Stack

- Laravel 10+
- PHP 8.1+
- TailwindCSS (customized)
- RESTful API via Drupal (headless setup)
- Blade views
- Custom controllers and models

## Installation

```bash
git clone https://github.com/your-org/peira.git
cd peira
composer install
cp .env.example .env
php artisan key:generate
# Set API URLs and keys in .env
php artisan serve
```

## Folder Structure

- `app/DataModels/`: `ProjectItem`, `NewsItem`, `ZitatItem`, `RowItem`, etc.
- `resources/views/`: Blade templates and partials
- `routes/web.php`: Routes and controller references
- `public/`: Entry point and frontend assets

## License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).