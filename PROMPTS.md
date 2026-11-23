# Prompts utilizados

Ferramenta em todos os prompts: **Claude (claude.ai)**.

## 1. Imagens de referência

- **Parte:** 2 — Front-end
- **Prompt:** Claude acesse o site oficial que estou querendo clonar e pegue as imagens para usar como mock enquanto ainda não implementei o backend
- **Como foi aproveitado:** as imagens foram salvas em `img/` (acomodações, experiências, serviços e destinos) e renomeadas no padrão `casa-01.jpg`, `exp-01.jpg` etc.

## 2. Tema escuro

- **Parte:** 2 — Front-end (CSS)
- **Prompt:** no css ja tenho as cores e fontes em variaveis, me ajuda a montar o tema escuro reaproveitando essas variaveis sem ter q duplicar tudo
- **Como foi aproveitado:** o tema escuro redefine as mesmas variáveis de cor do `:root` em `css/style.css`. A troca de tema e o salvamento da escolha foram feitos em `js/main.js`.

## 3. Revisão dos breakpoints

- **Parte:** 2 — Front-end (CSS)
- **Prompt:** revisa os breakpoints de tablet e celular e ve se tem algo quebrando no layout
- **Como foi aproveitado:** os ajustes sugeridos foram aplicados nas media queries de 1127px (tablet) e 743px (celular).

## 4. Dados fictícios

- **Parte:** 2 — Front-end (JavaScript)
- **Prompt:** gera uns dados ficticios pra preencher os arrays de acomodações, experiencias e serviços (nome, cidade, preço, nota, capacidade), umas 24 acomodações pra bater com as imagens
- **Como foi aproveitado:** os dados gerados viraram os arrays de `js/dados.js`, ajustados para a estrutura que já usávamos nos cards.

## 5. Desconto semanal

- **Parte:** 2 — Front-end (regras de negócio)
- **Prompt:** como faço o desconto semanal? a partir de 7 noites tira 10% das diarias e a taxa de serviço calcula depois do desconto
- **Como foi aproveitado:** a lógica entrou na função `calcularReserva` de `js/dados.js` (regra RN3), com as constantes `DESCONTO_SEMANAL` e `NOITES_DESCONTO`.

## 6. Setas do carrossel

- **Parte:** 2 — Front-end (JavaScript)
- **Prompt:** como faço as setas do carrossel sumirem quando chega no começo ou no fim da lista?
- **Como foi aproveitado:** a ideia foi usada na função `atualizarSetasCarrossel` de `js/main.js`.

## 7. Calendário de reserva

- **Parte:** 2 — Front-end (JavaScript)
- **Prompt:** me ajuda com o calendario de dois meses na reserva, quero clicar no check-in e no check-out e marcar o intervalo, e data passada n pode ser clicada
- **Como foi aproveitado:** a base foi adaptada em `js/anuncio.js` (funções `desenharMes` e `desenharCalendario`) e ligada ao resumo de preço da reserva.

## 8. Navegação da galeria pelo teclado

- **Parte:** 2 — Front-end (acessibilidade)
- **Prompt:** na galeria em modal coloca pra navegar com as setas do teclado
- **Como foi aproveitado:** foi adicionado o tratamento das teclas ← e → na função `iniciarGaleria` de `js/anuncio.js`.

## 9. Máscaras dos formulários

- **Parte:** 2 — Front-end (formulários)
- **Prompt:** preciso das mascaras de CPF, CEP, telefone e cartão no cadastro e no checkout, sem usar biblioteca
- **Como foi aproveitado:** as máscaras ficaram na função `aplicarMascaras` de `js/validacao.js`, reaproveitada por todos os formulários.

## 10. Validação de CPF e idade

- **Parte:** 2 — Front-end (formulários)
- **Prompt:** como valido se o CPF é valido de verdade (digitos verificadores) e se a pessoa tem mais de 18 pela data de nascimento?
- **Como foi aproveitado:** entraram as funções `cpfValido`, `idade` e `dataMaxima18` em `js/validacao.js`, usadas no cadastro.

## 11. Mensagens de erro acessíveis

- **Parte:** 2 — Front-end (formulários e acessibilidade)
- **Prompt:** quero as mensagens de erro embaixo de cada campo em vez de alert, e q o leitor de tela consiga ler tmb
- **Como foi aproveitado:** as mensagens passaram a ser criadas abaixo de cada campo, com `aria-live` e ligadas ao campo por `aria-describedby` (`js/validacao.js`).

## 12. Configuração do PHP e do banco

- **Ferramenta:** Claude Code
- **Parte:** 3 — Back-end (PHP)
- **Prompt:** leia só a parte 3 do trabalho preciso q vc crie os arquivos de config pra rodar o php e upe no github
- **Como foi aproveitado:** foram criados `includes/config.php` (conexão PDO), `database/clone_airbnb.sql` (tabelas e dados fictícios) e `Dockerfile`/`docker-compose.yml` para rodar PHP + MariaDB localmente. O README recebeu as instruções de execução.
