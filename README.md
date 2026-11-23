# Clone Airbnb

Mock funcional (clone) do [Airbnb](https://www.airbnb.com.br), desenvolvido para o Trabalho do III Bimestre da disciplina de Desenvolvimento Web I — IFC Campus Concórdia, Prof. Alisson Borges Zanetti.

> Projeto acadêmico, para fins educacionais, sem vínculo com o Airbnb. Todos os dados (acomodações, anfitriões, avaliações, usuários) são fictícios.

**Site publicado:** https://isaelsoares.github.io/clone-airbnb/

## Integrantes

- Natan Patzlaff
- Isael Santos
- Maria Jobim

## Sobre o site

O Airbnb é uma plataforma que conecta pessoas que querem alugar acomodações, experiências e serviços a anfitriões. O clone reproduz a navegação principal do site: busca e listagem de acomodações, página do anúncio com reserva, favoritos, login/cadastro, cadastro de anfitrião e checkout. Na Parte 3 o site passou a usar PHP e MySQL: as acomodações vêm do banco, os formulários são processados no servidor e há um painel administrativo com CRUD.

### Páginas

| Página | Arquivo | Descrição |
|---|---|---|
| Início | `index.php` | Carrosséis de acomodações por cidade e destinos de inspiração |
| Acomodações | `acomodacoes.php` | Listagem com busca e filtros por categoria e preço |
| Experiências | `experiencias.php` | Catálogo de experiências com modal de detalhes |
| Serviços | `servicos.php` | Catálogo e tabela de preços dos serviços |
| Anúncio | `anuncio.php` | Galeria de fotos, calendário de reserva e resumo de preço |
| Favoritos | `favoritos.php` | Itens salvos pelo usuário |
| Login / cadastro | `login.php` | Entrada e cadastro de usuários (sessão PHP) |
| Torne-se anfitrião | `anfitriao.php` | Formulário de anúncio (só para usuários logados) |
| Checkout | `checkout.php` | Confirmação e pagamento; grava a reserva no banco |

### Painel administrativo (`admin/`)

Acesso restrito ao administrador (link "Área administrativa" no rodapé ou `admin/login.php`).

| Página | Arquivo | Descrição |
|---|---|---|
| Login / sair | `admin/login.php`, `admin/sair.php` | Login com `$_SESSION`, `password_hash` / `password_verify` |
| Painel | `admin/index.php` | Totais de acomodações, reservas, usuários e valor reservado |
| Acomodações | `admin/acomodacoes.php` | Lista com busca (GET) e exclusão (POST) |
| Nova / editar acomodação | `admin/acomodacao.php` | Formulário de cadastro e edição |
| Reservas | `admin/reservas.php` | Reservas gravadas pelo checkout |
| Usuários | `admin/usuarios.php` | Usuários cadastrados pelo site |

### Funcionalidades

- Menu responsivo e busca adaptada ao celular
- Acomodações lidas do MySQL pelo PHP e entregues ao JavaScript, que monta os cards e os filtros
- Experiências, serviços e destinos gerados por JavaScript a partir de arrays de objetos (`js/dados.js`)
- Busca e filtro de acomodações por categoria e faixa de preço
- Galeria de fotos em modal e carrosséis navegáveis
- Favoritos salvos no `localStorage`
- Tema claro e escuro, salvo entre as visitas
- Calendário de dois meses para escolher check-in e check-out
- Validação dos formulários no cliente, com mensagens na página, máscaras (CPF, CEP, telefone, cartão) e verificação de CPF e idade mínima
- Validação repetida no servidor (PHP), com mensagens de erro e sucesso e os campos mantidos preenchidos quando há erro
- Cadastro e login de usuários com senha em `password_hash`; sessão com `$_SESSION`
- Painel com CRUD de acomodações (criar, listar, editar, excluir), lista de reservas e de usuários

### Regras de negócio

1. O check-out deve ser posterior ao check-in, o check-in não pode ser no passado e a estadia deve respeitar o mínimo de noites do anúncio.
2. O número de hóspedes não pode ultrapassar a capacidade da acomodação.
3. Estadias de 7 noites ou mais recebem 10% de desconto nas diárias.

As regras 1 e 2 são conferidas em `js/dados.js` (cliente) e de novo em `includes/funcoes.php` (`validarReserva`) antes de gravar a reserva; o total com a regra 3 é sempre calculado no PHP (`calcularReserva`). No painel, uma acomodação não pode ter mais de 2 hóspedes por cama e não pode ser excluída se tiver reservas.

## Tecnologias

- HTML5, CSS3 e JavaScript próprios (sem frameworks)
- PHP 8 com PDO (prepared statements) e MySQL/MariaDB
- [Google Fonts](https://fonts.google.com) (Roboto) e [Bootstrap Icons](https://icons.getbootstrap.com)

## Estrutura de pastas

```
├── admin/                # painel administrativo (área restrita)
├── css/style.css         # guia de estilo: variáveis, componentes, layout e tema escuro
├── database/             # clone_airbnb.sql: criação do banco e dados fictícios
├── includes/
│   ├── config.php        # conexão PDO e sessão
│   ├── funcoes.php       # funções auxiliares, validações e regras de negócio
│   ├── dados.php         # acesso ao banco (consultas com prepared statements)
│   ├── cabecalho.php     # <head>, cabeçalho e menu (reutilizado por todas as páginas)
│   ├── rodape.php        # rodapé e scripts
│   └── protege.php       # bloqueia as páginas do painel sem login
├── js/                   # scripts (dados, regras de negócio, validação e um por página)
├── img/                  # imagens das acomodações, experiências, serviços e destinos
├── wireframes/           # wireframes da Parte 1
├── *.php                 # páginas do site
└── PROMPTS.md            # prompts de IA utilizados
```

## Como usar

**Online (versão estática da Parte 2):** acesse o [site publicado](https://isaelsoares.github.io/clone-airbnb/). O GitHub Pages não executa PHP, então o back-end da Parte 3 roda localmente (abaixo).

**Localmente com PHP (Parte 3):**

Clone o repositório:

```
git clone https://github.com/isaelsoares/clone-airbnb.git
```

*Opção A — Docker* (PHP 8.3 + MariaDB, banco criado automaticamente):

```
docker compose up -d
```

Acesse http://localhost:8080. Para recriar o banco do zero: `docker compose down -v` e suba de novo.

*Opção B — XAMPP:*

1. Copie a pasta para `C:\xampp\htdocs\clone-airbnb` e inicie Apache e MySQL.
2. No phpMyAdmin, importe `database/clone_airbnb.sql`.
3. Acesse http://localhost/clone-airbnb.

A conexão fica em `includes/config.php` (padrão do XAMPP: usuário `root`, sem senha). Em hospedagem, altere os valores padrão de `DB_HOST`, `DB_NAME`, `DB_USER` e `DB_PASS` nesse arquivo.

### Acesso de teste

- **Painel administrativo** (`/admin/login.php`): `admin@clone.com` / `admin12345`
- **Usuário do site:** crie uma conta em "Cadastrar-se" (`login.php#cadastro`)
