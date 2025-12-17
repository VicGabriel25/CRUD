# CRUD Mundo (prjDeep)

Sistema de gerenciamento geográfico (países/cidades) — versão local do projeto.

## Sobre
Projeto em PHP que contém uma API simples e uma interface frontend para listar, criar e gerenciar países e cidades, além de um mapa interativo e integração com APIs externas (RestCountries, OpenWeatherMap).

## Preparação local
1. Coloque os arquivos na pasta pública do seu servidor (ex.: XAMPP `htdocs/prjDeep`).
2. Crie o banco de dados e importe `dbmundo.sql`.
3. Atualize credenciais em `config/database.php` se necessário.
4. Configure sua chave do OpenWeather em `config/api_keys.php` (ou deixe o placeholder para usar clima simulado).

## Antes de commitar
- Confirme se `config/api_keys.php` não contém chaves reais (ele já vem com placeholder).
- Se você tiver credenciais locais, remova-as ou use variáveis de ambiente (o arquivo `config/database.php` está listado no `.gitignore`).

## Comandos para criar o repositório Git e subir ao GitHub
Opção A — (recomendado) com `gh` (GitHub CLI):

```bash
# inicializar e commitar tudo
git init
git add .
git commit -m "Initial commit"
# criar repo público e fazer o push imediatamente (substitua o nome do repo se necessário)
gh repo create VicGabriel25/CRUD --public --source=. --remote=origin --push
```

Opção B — sem `gh`, criando via web ou API (requer PAT):

1) Criar o repositório pelo site do GitHub (https://github.com/new) com nome `CRUD` e visibilidade `Public`.
2) Executar:

```bash
git init
git add .
git commit -m "Initial commit"
git branch -M main
git remote add origin https://github.com/VicGabriel25/CRUD.git
git push -u origin main
```

Opção C — criar via API usando `curl` (requer PAT):

```bash
curl -H "Authorization: token YOUR_PAT" \
  https://api.github.com/user/repos \
  -d '{"name":"CRUD","private":false}'

# então:
git remote add origin https://github.com/VicGabriel25/CRUD.git
git push -u origin main
```

## Observações
- Se quiser que eu faça o push por você, me envie um PAT com scope `repo` (eu posso usar somente para criar o repositório remoto e dar o push) — caso prefira, eu também posso fornecer o passo a passo e você executa localmente.
- Posso também adicionar arquivos úteis (ex.: `LICENSE`, GitHub Actions) se desejar.
