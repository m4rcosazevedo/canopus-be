# AGENTS.md
## Laravel 12 + Docker – Project Agent Guide

Este documento define como humanos e agentes de IA devem interagir com este projeto.
Ele deve ser considerado **fonte de verdade** para decisões técnicas, padrões e workflows.

---

## 1. Stack Principal

- **PHP**: 8.3+
- **Laravel**: 12.x
- **Banco de dados**: MySQL 8 / PostgreSQL 15 (ver docker-compose)
- **Cache / Queue**: Redis
- **Frontend**: Blade / Inertia / API-only (ver seção 3)
- **Containerização**: Docker + Docker Compose

---

## 2. Ambiente de Desenvolvimento (Docker)

### Subir o ambiente
```bash
docker compose up -d
```

### Parar containers
```bash
docker compose down
```

### Executar comandos Laravel
```bash
docker compose exec app php artisan migrate
docker compose exec app php artisan test
docker compose exec app php artisan queue:work
```
> ⚠️ Nunca execute PHP ou Composer diretamente fora do container.

--- 

## 3. Tipo de Aplicação

### Este projeto é classificado como:
- API + Frontend separado

👉 Agentes de IA devem respeitar este modelo ao gerar código.

---

## 4. Padrões Arquiteturais
### Camadas

- Controllers finos
- Lógica de negócio em Services
- Regras complexas fora de Models

Exemplo esperado:
```md
Controller → Service → Repository
```

### ❌ Evitar
- Lógica complexa em Controllers
- Facades fora de Controllers

---

## 5. Padrões de Código

- PSR-12 obrigatório
- Tipagem forte sempre que possível
- Métodos pequenos e coesos

### Naming
Services: UserService
Jobs: ProcessUserImportJob
Events: UserRegistered

## 6. Banco de Dados

- Migrations sempre idempotentes
- Nunca alterar migrations já rodadas
- Evitar queries N+1 (usar eager loading)

---

## 8. Filas, Jobs e Eventos

- Jobs devem ser idempotentes
- Usar retry e timeout
- Eventos não devem conter lógica de negócio

--- 

## 9. Segurança

- Nunca logar dados sensíveis
- Usar Policies para autorização
- Validação sempre via Form Requests
- Nunca confiar em input do usuário

## 10. Integrações Externas

- Todas integrações devem usar Clients dedicados
- Nenhuma chamada HTTP direta em Controllers
- Timeouts e retries obrigatórios

## 11. Observabilidade

- Logs estruturados
- Exceptions sempre rastreáveis
- Usar context (request_id, user_id)

## 12. Instruções para Agentes de IA (IMPORTANTE)

### Ao gerar código, agentes de IA devem:
- Respeitar Laravel 12
- Respeitar Docker (nunca assumir ambiente local)
- Seguir a arquitetura descrita
- Priorizar legibilidade sobre “código esperto”
- Nunca quebrar backward compatibility sem aviso
- Explicar decisões arquiteturais quando relevante

## 13. O que NÃO fazer

- ❌ Criar arquivos fora da estrutura Laravel
- ❌ Ignorar este documento
- ❌ Assumir versões diferentes das listadas
- ❌ Introduzir dependências sem justificar

## 14. Atualização deste documento

Este arquivo deve ser atualizado sempre que:

A stack mudar

Um padrão arquitetural novo for adotado

O Docker sofrer alterações

Uma decisão técnica importante for tomada

---

# 🔄 Como manter o AGENTS.md **sempre atualizado**

### ✅ 1. Regra de ouro
> **Toda decisão arquitetural relevante = update no AGENTS.md**

---

### ✅ 2. Check automático em PR
Inclua no checklist do Pull Request:
- [ ] Mudança arquitetural?
- [ ] Docker alterado?
- [ ] Stack mudou?
  👉 Se sim, **AGENTS.md atualizado**

---

### ✅ 3. Use IA para validar o próprio AGENTS.md
Prompt poderoso:
```
Revise este AGENTS.md e sugira melhorias considerando Laravel 12 e boas práticas modernas.
```

---

### ✅ 4. Versione decisões importantes
Se quiser ir além:
- `/docs/adr/0001-queue-strategy.md`
- Referencie ADRs dentro do AGENTS.md

---

## 🏆 Resultado final

Com esse AGENTS.md você terá:

✅ Onboarding rápido  
✅ IA muito mais inteligente  
✅ Código consistente  
✅ Menos decisões repetidas  
✅ Mais foco em negócio

---
