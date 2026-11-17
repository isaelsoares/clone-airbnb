# Clone Airbnb

Mock funcional (clone) do [Airbnb](https://www.airbnb.com.br), desenvolvido para o Trabalho do III Bimestre da disciplina de Desenvolvimento Web I — IFC Campus Concórdia, Prof. Alisson Borges Zanetti.

> Projeto acadêmico, para fins educacionais, sem vínculo com o Airbnb. Todos os dados (acomodações, anfitriões, avaliações, usuários) são fictícios.

**Site publicado:** https://isaelsoares.github.io/clone-airbnb/

## Integrantes

- Natan Patzlaff
- Isael Santos
- Maria Jobim

## Sobre o site

O Airbnb é uma plataforma que conecta pessoas que querem alugar acomodações, experiências e serviços a anfitriões. O clone reproduz a navegação principal do site: busca e listagem de acomodações, página do anúncio com reserva, favoritos, login/cadastro, cadastro de anfitrião e checkout.

### Páginas

| Página | Arquivo | Descrição |
|---|---|---|
| Início | `index.html` | Carrosséis de acomodações por cidade e destinos de inspiração |
| Acomodações | `acomodacoes.html` | Listagem com busca e filtros por categoria e preço |
| Experiências | `experiencias.html` | Catálogo de experiências com modal de detalhes |
| Serviços | `servicos.html` | Catálogo e tabela de preços dos serviços |
| Anúncio | `anuncio.html` | Galeria de fotos, calendário de reserva e resumo de preço |
| Favoritos | `favoritos.html` | Itens salvos pelo usuário |
| Login / cadastro | `login.html` | Formulários de entrada e cadastro (área restrita) |
| Torne-se anfitrião | `anfitriao.html` | Formulário de cadastro de acomodação (área restrita) |
| Checkout | `checkout.html` | Confirmação e pagamento da reserva |

### Funcionalidades

- Menu responsivo e busca adaptada ao celular
- Conteúdo gerado por JavaScript a partir de arrays de objetos (`js/dados.js`)
- Busca e filtro de acomodações por categoria e faixa de preço
- Galeria de fotos em modal e carrosséis navegáveis
- Favoritos salvos no `localStorage`
- Tema claro e escuro, salvo entre as visitas
- Calendário de dois meses para escolher check-in e check-out
- Validação dos formulários no cliente, com mensagens na página, máscaras (CPF, CEP, telefone, cartão) e verificação de CPF e idade mínima

### Regras de negócio

1. O check-out deve ser posterior ao check-in, o check-in não pode ser no passado e a estadia deve respeitar o mínimo de noites do anúncio.
2. O número de hóspedes não pode ultrapassar a capacidade da acomodação.
3. Estadias de 7 noites ou mais recebem 10% de desconto nas diárias.

## Tecnologias

- HTML5, CSS3 e JavaScript próprios (sem frameworks)
- [Google Fonts](https://fonts.google.com) (Roboto) e [Bootstrap Icons](https://icons.getbootstrap.com)

## Estrutura de pastas

```
├── css/style.css     # guia de estilo: variáveis, componentes, layout e tema escuro
├── js/               # scripts (dados, regras de negócio, validação e um por página)
├── img/              # imagens das acomodações, experiências, serviços e destinos
├── wireframes/       # wireframes da Parte 1
├── *.html            # páginas do site
└── PROMPTS.md        # prompts de IA utilizados
```

## Como usar

**Online:** acesse o [site publicado](https://isaelsoares.github.io/clone-airbnb/).

**Localmente:**

1. Clone o repositório:
   ```
   git clone https://github.com/isaelsoares/clone-airbnb.git
   ```
2. Abra o `index.html` no navegador, ou use a extensão Live Server do VS Code.

Não há back-end nesta etapa: login, cadastro e reserva são validados no navegador e não gravam dados em servidor.
