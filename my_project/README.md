# Instalação

- clonar o projeto

- Instalar os pacotes php
```env
composer install
```

- Configure o banco de dados em .env.local

- Criar o banco de dados depois de configurar
```env
php bin/console doctrine:database:create
```

- Rodar as Migrations
```env
php bin/console doctrine:migrations:migrate
```

