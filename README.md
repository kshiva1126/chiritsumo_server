# chiritsumo-server

## 環境構築

```
$ git clone https://github.com/kshiva1126/chiritsumo_server.git
$ docker-compose up -d --build
$ docker-compose exec app composer install
$ cp src/.env.sample src/.env
```

## ER図
![ER-Diagram](./out/er-diagram/er-diagram.png)
