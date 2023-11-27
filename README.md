## Teste Tecnico Jorge Carlos

O teste é uma api com Laravel com admin em Vuejs.

A aplicação faz a criação, listagem, atualizar, deletar e importação csv de Pacientes e salva no banco de dados.

Tambem possui uma imagem docker customizada.

## A aplicação

- Cache com redis
- Fila com redis e Laravel Horizon
- Busca com Elasticsearch
- Importação de pacientes por csv
- Testes de api para todas as rotas do sistema.
- Supervisord para abrir o server e o laravel horizon

## Urls

Aplicação: [http//localhost:8000]()

Horizon: [http//localhost:8000/horizon]()

## Uso

Rodar containers dockers

```bash
$ docker compose up -d
```

### Na maquina docker:

Instalar dependencias composer

```bash
$ composer install
```

Reiniciar containers para remover os erros do supervisor da ausencia das dependencias do composer

-

Instalar dependencias npm

```bash
$ npm install
```

Compilar Vuejs

```bash
$ npm run build
```
