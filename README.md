# Sistema de Depósito - Objetivos e Funcionalidades
<p>O Sistema de Depósito tem por objetivo o cadastro de resumos de Monografias de Trabalhos de Conclusão de Curso da Graduação.</p>
<p>O acesso ao sistema é baseado no vínculo com a USP que a pessoa tem, bem como as funcionalidades liberada para cada um deles. 
O Login é realizado através da senha única.</p>
<p>Aluno de Graduação (ALUNOGR)<br/>
<ul>
    <li>Cadastra e modifica o resumo do TCC dentro de prazo estipulado pela CG</li>
    <li>Corrige monografia conforme solicitação do avaliador</li>
</ul>
</p>
<p>Orientador (todos os docentes cadastrados como orientadores neste sistema)<br/>
<ul>
    <li>Aprova ou Reprova Monografia para ser mandada para membro da Comissão (Avaliador)</li>
</ul>
</p>
<p>Avaliador (todos os docentes cadastrados como membros da Comissão neste sistema)<br/>
<ul>
    <li>Avalia e corrige a Monografia</li>
    <li>Aprova as correções</li>
</ul>
</p>
<p>Graduação<br/>
<ul>
    <li>Na monografia, edita informações de título, resumo, descritores, áreas temáticas e se o trabalho é passível de publicação</li>
    <li>Cadastra Orientadores</li>
    <li>Cadastra Palavras Chaves</li>
    <li>Cadastra áreas temáticas</li>
    <li>Cadastra Comissão de Graduação</li>
    <li>Emite Declarações</li>
    <li>Parametriza datas para prazo de cadastro e avaliação de TCC (Área Administrativa)</li>
</ul>
</p>

## Dependências

<p>
Os projetos da iniciativa uspdev que estão sendo usados

* [uspdev/replicado](https://github.com/uspdev/replicado)
* [uspdev/senha-unica-socialite](https://github.com/uspdev/senhaunica-socialite)
* [uspdev/laravel-usp-theme](https://github.com/uspdev/laravel-usp-theme)

Projeto [laravel/excel](https://laravel-excel.com/)  
Laravel 8.83, PHP 8.1.24
</p>

## Instalação ambiente DEV

<p>Copiar arquivo .env.example para .env e ajustar as diretivas (APP_NAME, APP_URL, senhas de BD, replicado, SMTP, etc.)</p>

<p>No diretório onde consta o projeto, rodar o seguinte comando (AMBIENTE DEV):</p>

```
composer install --ignore-platform-reqs
php artisan migrate
php artisan key:generate

```
