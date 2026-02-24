# 🤖 AGENTS.md

## 1. Visão Geral e Manifesto

Este documento é a **Single Source of Truth (SSoT)** para o projeto. Ele define os padrões arquiteturais, de codificação e de testes.

> **Regra de Ouro para IAs:** Se uma instrução aqui conflitar com seu conhecimento prévio, **este documento prevalece**. Na dúvida, interrompa a geração e pergunte.

---

## 2. Stack Tecnológica (Hard Constraints)

* **Runtime:** PHP 8.4+
* **Framework:** Laravel 12.x
* **Ambiente:** Docker + Docker Compose
* **Database:** MySQL 8.0+
* **Cache/Queue:** Redis
* **Testes:** Pest PHP
* **Arquitetura:** Modular Monolith (Localizado em `app/Modules`)
* **Frontend**: API-only
* 
---

## 3. Arquitetura Modular (`app/Modules`)

Para garantir a escalabilidade, **nenhuma feature nova deve ser criada na estrutura padrão do Laravel (`app/Http`, `app/Models`, etc.)**. Toda nova funcionalidade deve residir em seu próprio módulo.

### Estrutura de um Módulo:

Siga rigorosamente este scaffold para cada módulo em `app/Modules/{ModuleName}`:

```text
app/Modules/{ModuleName}/
├── Http/
│   ├── Controllers/
│   ├── Requests/
│   └── Resources/
├── Console/ (Commands específicos do módulo)
├── Models/
├── Services/
├── Actions/ (Lógica de negócio atômica)
├── DataTransferObjects/ (DTOs para transporte de dados)
├── Events/
├── Repositories/
├── Persistence/ (Migrations/Seeders específicos se necessário)
├── Providers/ (Onde o módulo se registra no Laravel)
└── Tests/
    ├── Feature/
    └── Unit/

```

---

## 4. Estratégia de Testes (Definição de Pronto)

**Nenhum código é considerado "concluído" sem testes correspondentes.**

* **TDD Mentality:** Ao sugerir uma feature, a IA deve primeiro esboçar o caso de teste.
* **Cobertura:** * **Unitários:** Para `Actions`, `Services` e `Custom Validation Rules`.
* **Feature:** Para todos os endpoints de API e fluxos de usuário.


* **Mocking:** Use mocks apenas para integrações externas (Gateways de pagamento, APIs de terceiros). Banco de dados deve ser testado em memória ou via RefreshDatabase.

---

## 5. Padrões de Implementação (Anti-Alucinação)

### 5.1. Camada de Serviço e Actions

* **Controllers:** Devem ter no máximo 3 linhas (chamar um Action/Service e retornar Resource).
* **Actions:** Use para lógica que faz apenas uma coisa (ex: `CreateUserAction`).
* **Services:** Use para orquestrar múltiplos Actions ou lógica complexa de domínio.

### 5.2. Tipagem e Segurança

* Use `readonly` para DTOs e propriedades injetadas no construtor.
* Sempre defina tipos de retorno em todos os métodos.
* **Validation:** Nunca valide dados dentro do Controller; use `FormRequest` específicos dentro da pasta `UI/Http/Requests`.

---

## 6. Workflow Docker & Comandos

As IAs não devem assumir que o ambiente local possui PHP instalado. Todos os comandos devem ser prefixados para rodar no container.

### Criando novas features
Sempre utilize o comando customizado para iniciar um módulo:
```bash
docker compose exec app php artisan make:module NomeDoModulo
```

*Nota*: Após criar, registre o Provider em bootstrap/providers.php.

```bash
docker compose exec app php artisan migrate

docker compose exec app php artisan test

docker compose exec app php artisan queue:work

# Execução de Testes (Obrigatório antes de qualquer refatoração)
docker compose exec app php artisan test

# Criação de Módulos (Manual ou via script se existir)
# Lembre-se: Criar em app/Modules/{Name}

```
> ⚠️ Nunca execute PHP ou Composer diretamente fora do container.

---

## 7. Regras para o Agente de IA (Protocolo de Verificação)

Para evitar alucinações e código legado, a IA deve seguir este checklist antes de entregar qualquer output:

1. **Context Check:** "Estou criando isso dentro de `app/Modules`?"
2. **Version Check:** "Este código utiliza syntax do Laravel 12 (ex: novas facades, helpers simplificados)?"
3. **Test Check:** "Eu incluí os arquivos de teste para esta nova lógica?"
4. **Security Check:** "Dados sensíveis estão sendo tratados via DTO ou Request?"
5. **Hallucination Check:** "Este método/classe realmente existe no Laravel 12 ou estou inventando?"

---

## 8. O que NÃO fazer (Red Flags)

* ❌ Criar Models em `app/Models`.
* ❌ Usar logic dentro de Controllers ou arquivos de rota.
* ❌ Ignorar o uso de `strict_types=1`.
* ❌ Sugerir pacotes externos sem verificar se o Laravel 12 já possui a funcionalidade nativamente.
* ❌ Alterar arquivos de migração que já foram commitados.

---

## 9. Manutenção do AGENTS.md

Este arquivo deve ser atualizado via Pull Request sempre que:

1. Um novo padrão de design (ex: Repository Pattern) for adotado.
2. A versão de uma dependência crítica mudar.
3. O fluxo de CI/CD sofrer alterações que impactem o desenvolvimento.

---


