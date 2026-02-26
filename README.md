# Locadora API

API REST para gerenciamento de locação de veículos. Desenvolvida como projeto de portfólio para consolidar conceitos de arquitetura em camadas com Laravel — repository pattern, autenticação por token, políticas de acesso e testes automatizados.

A ideia surgiu de uma conversa com o dono de uma locadora pequena que controlava tudo em planilhas do Excel: qual carro estava disponível, quem tinha alugado, quilometragem de saída e retorno. O sistema nunca chegou a ser implantado, mas serviu de base pra eu estruturar uma API de verdade com as preocupações que aparecem em projetos reais — race condition no momento do aluguel, soft delete pra não perder histórico, multa por atraso na devolução.

---

## Tecnologias

- PHP 8.4 / Laravel 12
- MySQL 8
- Laravel Sanctum para autenticação via Bearer token
- PHPUnit com SQLite em memória para os testes
- GitHub Actions para CI (roda os testes a cada push)

---

## Arquitetura

```
app/
├── Http/
│   ├── Controllers/     # Recebem a requisição, delegam para os repositórios
│   ├── Requests/        # Validação de entrada
│   ├── Resources/       # Formatação da resposta JSON
│   └── Middleware/
├── Models/              # Eloquent com SoftDeletes
├── Repositories/
│   ├── Contracts/       # Interfaces
│   └── Eloquent/        # Implementações
├── Policies/            # Autorização por recurso
└── Exceptions/          # Erros de negócio mapeados para HTTP
```

Os controllers dependem das interfaces, não das implementações. O binding fica no `AppServiceProvider`. Isso facilita trocar a implementação (ex: cache) sem tocar no controller.

---

## Instalação

**Pré-requisitos:** PHP 8.4+, Composer, MySQL 8

```bash
git clone <repositório>
cd car-rental-app
composer install
cp .env.example .env
php artisan key:generate
```

Configure o banco no `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=locadora
DB_USERNAME=root
DB_PASSWORD=sua_senha
```

Execute as migrations:

```bash
php artisan migrate
```

Suba o servidor:

```bash
php artisan serve
```

API disponível em `http://localhost:8000/api`

Documentação interativa (Swagger UI): `http://localhost:8000/docs`

---

## Autenticação e permissões

A API usa Laravel Sanctum. Todas as rotas — exceto `/register` e `/login` — exigem o header:

```
Authorization: Bearer {token}
```

O token é gerado no registro e regenerado a cada login. Ao fazer logout, todos os tokens do usuário são revogados.

Existem dois papéis:

| Papel | Permissões |
|-------|------------|
| `admin` | Acesso total, incluindo exclusão de marcas, linhas e veículos |
| `operador` | Leitura e operações (criar, atualizar, locar, devolver), sem exclusão de marcas/linhas/veículos |

O papel padrão ao registrar é `operador`.

### Registrar

```http
POST /api/register
Content-Type: application/json

{
    "name": "Carlos Mendes",
    "email": "carlos@locadora.com",
    "password": "minhasenha",
    "password_confirmation": "minhasenha"
}
```

```json
{
    "token": "abc123..."
}
```

### Login

```http
POST /api/login
Content-Type: application/json

{
    "email": "carlos@locadora.com",
    "password": "minhasenha"
}
```

```json
{
    "token": "xyz456..."
}
```

### Logout

```http
POST /api/logout
Authorization: Bearer xyz456...
```

---

## Endpoints

### Marcas

| Método | Rota | Descrição |
|--------|------|-----------|
| GET | `/api/brands` | Lista marcas (paginado, 15 por página) |
| GET | `/api/brands?name=toy` | Busca por nome |
| GET | `/api/brands/{id}` | Detalhe da marca |
| POST | `/api/brands` | Criar marca |
| PUT | `/api/brands/{id}` | Atualizar marca |
| DELETE | `/api/brands/{id}` | Remover marca (soft delete) |

**Criar marca:**
```http
POST /api/brands
Authorization: Bearer {token}

{
    "name": "Toyota",
    "image": "toyota.png"
}
```

---

### Linhas (modelos)

| Método | Rota | Descrição |
|--------|------|-----------|
| GET | `/api/lines` | Lista linhas |
| GET | `/api/lines?brand_id=1` | Filtra por marca |
| GET | `/api/lines/{id}` | Detalhe |
| POST | `/api/lines` | Criar linha |
| PUT | `/api/lines/{id}` | Atualizar |
| DELETE | `/api/lines/{id}` | Remover |

**Criar linha:**
```http
POST /api/lines
Authorization: Bearer {token}

{
    "brand_id": 1,
    "name": "Corolla",
    "image": "corolla.png",
    "door_count": 4,
    "seats": 5,
    "air_bag": true,
    "abs": true
}
```

---

### Veículos

| Método | Rota | Descrição |
|--------|------|-----------|
| GET | `/api/cars` | Lista veículos |
| GET | `/api/cars?available=1` | Somente disponíveis |
| GET | `/api/cars?plate=abc` | Busca por placa |
| GET | `/api/cars/{id}` | Detalhe |
| POST | `/api/cars` | Cadastrar veículo |
| PUT | `/api/cars/{id}` | Atualizar |
| DELETE | `/api/cars/{id}` | Remover (bloqueia se tiver locação ativa) |

**Cadastrar veículo:**
```http
POST /api/cars
Authorization: Bearer {token}

{
    "line_id": 1,
    "plate": "ABC-1D23",
    "available": true,
    "km": 15000
}
```

---

### Clientes

| Método | Rota | Descrição |
|--------|------|-----------|
| GET | `/api/clients` | Lista clientes |
| GET | `/api/clients?name=maria` | Busca por nome |
| GET | `/api/clients/{id}` | Detalhe |
| POST | `/api/clients` | Cadastrar cliente |
| PUT | `/api/clients/{id}` | Atualizar |
| DELETE | `/api/clients/{id}` | Remover (bloqueia se tiver locação ativa) |

**Cadastrar cliente:**
```http
POST /api/clients
Authorization: Bearer {token}

{
    "name": "Maria Oliveira",
    "cpf": "123.456.789-00",
    "email": "maria@email.com",
    "phone": "(51) 99999-1234"
}
```

---

### Locações

| Método | Rota | Descrição |
|--------|------|-----------|
| GET | `/api/rentals` | Lista locações |
| GET | `/api/rentals/{id}` | Detalhe |
| POST | `/api/rentals` | Criar locação |
| PUT | `/api/rentals/{id}` | Atualizar / registrar devolução |
| DELETE | `/api/rentals/{id}` | Remover |

**Criar locação:**
```http
POST /api/rentals
Authorization: Bearer {token}

{
    "client_id": 1,
    "car_id": 1,
    "period_start_date": "2026-03-01 08:00:00",
    "period_expected_end_date": "2026-03-05 08:00:00",
    "daily_rate": 200.00,
    "initial_km": 15000
}
```

A criação verifica disponibilidade do carro e atualiza `available = false` dentro de uma transação para evitar race condition.

**Registrar devolução:**
```http
PUT /api/rentals/{id}
Authorization: Bearer {token}

{
    "period_actual_end_date": "2026-03-07 08:00:00",
    "final_km": 15800
}
```

Se a devolução for após a data prevista, o campo `late_fee` é calculado automaticamente (50% da diária por dia de atraso). O carro volta para `available = true` e o km é atualizado.

**Exemplo de resposta:**
```json
{
    "data": {
        "id": 1,
        "period_start_date": "2026-03-01 08:00:00",
        "period_expected_end_date": "2026-03-05 08:00:00",
        "period_actual_end_date": "2026-03-07 08:00:00",
        "daily_rate": 200,
        "initial_km": 15000,
        "final_km": 15800,
        "late_fee": 200,
        "total": 1000,
        "client": {
            "id": 1,
            "name": "Maria Oliveira",
            "cpf": "123.456.789-00",
            "email": "maria@email.com",
            "phone": "(51) 99999-1234"
        },
        "car": {
            "id": 1,
            "plate": "ABC-1D23",
            "available": true,
            "km": 15800,
            "line": { ... }
        }
    }
}
```

---

## Regras de negócio

- Carro indisponível → 422 ao tentar criar locação
- `final_km` menor que `initial_km` → 422
- Data de devolução anterior à data de início → 422
- Deletar carro ou cliente com locação em aberto → 422
- Devolução com atraso → `late_fee` = dias de atraso × diária × 0.5
- Todos os registros usam soft delete — nada é removido fisicamente do banco

---

## Banco de dados

```
users
  id, name, email, password, role (admin|operador), timestamps
  personal_access_tokens  (Sanctum)

brands
  id, name (único), image, deleted_at, timestamps

lines
  id, brand_id, name, image, door_count, seats, air_bag, abs, deleted_at, timestamps

cars
  id, line_id, plate (único), available, km, deleted_at, timestamps

clients
  id, name, cpf (único), email (único), phone, deleted_at, timestamps

rentals
  id, client_id, car_id, period_start_date, period_expected_end_date,
  period_actual_end_date (nullable), daily_rate, initial_km,
  final_km (nullable), deleted_at, timestamps
```

---

## Testes

Os testes rodam em SQLite em memória — não precisam de banco configurado.

```bash
php artisan test
```

Ou diretamente com PHPUnit:

```bash
./vendor/bin/phpunit
```

Cobre autenticação, CRUD de marcas (incluindo restrição de exclusão por papel), e o ciclo completo de locação (criar, devolver, multa por atraso, validações de km e data, proteção de delete com locação ativa).

---

## Erros comuns

**401 em todas as rotas:** token não enviado ou inválido. Faça login novamente.

**422 ao criar cliente:** CPF deve estar no formato `000.000.000-00` e email precisa ser único.

**422 ao criar locação:** verifique se o `car_id` existe e se `available` é `true`.

**Migration falhou:** rode `php artisan migrate:fresh` para recriar tudo do zero (apaga os dados).

---

## Licença

MIT
