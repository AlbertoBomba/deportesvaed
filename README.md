<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## Escaparate de DeportesVaed

El front adapta [Lunar Livewire Starter Kit](https://github.com/lunarphp/livewire-starter-kit)
(revision `db4ad00a68c61f57290b5b3a2060c8bd801f867f`) a Laravel 13, Lunar 1.5,
Livewire 3 y Tailwind 4. Es una adaptacion local, no una dependencia actualizable
del starter. La atribucion y licencia estan en [LICENSE-LUNAR-STARTER](LICENSE-LUNAR-STARTER).

Incluye portada, colecciones, busqueda por nombre/SKU, ficha con variantes,
carrito y checkout de invitado con pago offline. El backoffice sigue en `/lunar`.
Las vistas iniciales mantienen el ingles del starter.

### Desarrollo

Ejecuta estos comandos en el entorno que tenga acceso a tu base de datos
configurada. La integracion no cambia esa conexion ni necesita migraciones nuevas.

```sh
composer install
npm ci
npm run build
composer run dev
```

Configura `APP_NAME` en tu entorno para mostrar el nombre de la tienda.
Para desarrollo de estilos tambien puedes usar `npm run dev`.

### Catalogo y checkout

- Utiliza los datos existentes: no ejecutes los seeders de la demo ni
  `migrate:fresh` sobre tu base de datos.
- Los productos deben estar publicados, tener URL por defecto y estar visibles
  en el canal y grupo de clientes de la sesion de Lunar. Las colecciones tambien
  deben tener URL y disponibilidad. Configura precios, moneda, impuestos,
  variantes, inventario e imagenes desde Lunar.
- La busqueda usa la base de datos; no requiere Meilisearch ni importar un indice.
- El checkout usa `cash-in-hand` de `config/lunar/payments.php`: crea un pedido
  con pago offline pendiente, no cobra tarjetas ni incluye Stripe.
- Los envios usan exclusivamente las opciones que proporcione `ShippingManifest`
  de Lunar. No se registra la tarifa ficticia del starter. Si no tienes un
  proveedor/modificador de envios configurado, se muestra un aviso y no se puede
  finalizar un pedido fisico. Los productos digitales no requieren envio.
- La confirmacion solo muestra el ultimo pedido realizado desde esa sesion.
- El starter original es una demostracion. Antes de publicar, revisa metodos de
  envio, impuestos, pago, correos transaccionales y textos legales de tu tienda.

### Validacion

```sh
php artisan test --compact
npm run build
```

Las pruebas usan SQLite en memoria y no modifican el catalogo real.

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework.

In addition, [Laracasts](https://laracasts.com) contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

You can also watch bite-sized lessons with real-world projects on [Laravel Learn](https://laravel.com/learn), where you will be guided through building a Laravel application from scratch while learning PHP fundamentals.

## Agentic Development

Laravel's predictable structure and conventions make it ideal for AI coding agents like Claude Code, Cursor, and GitHub Copilot. Install [Laravel Boost](https://laravel.com/docs/ai) to supercharge your AI workflow:

```bash
composer require laravel/boost --dev

php artisan boost:install
```

Boost provides your agent 15+ tools and skills that help agents build Laravel applications while following best practices.

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
