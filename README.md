# Twilight: o devorador de rinhas

![GitHub repo size](https://img.shields.io/github/repo-size/opencodeco/twilight?style=for-the-badge)
![GitHub language count](https://img.shields.io/github/languages/count/opencodeco/twilight?style=for-the-badge)
![GitHub forks](https://img.shields.io/github/forks/opencodeco/twilight?style=for-the-badge)
![Bitbucket open issues](https://img.shields.io/bitbucket/issues/opencodeco/twilight?style=for-the-badge)
![Bitbucket open pull requests](https://img.shields.io/bitbucket/pr-raw/opencodeco/twilight?style=for-the-badge)

> Projeto montado apenas em cima do Swoole e com muitas soluções in house para ganhar performance

### Ajustes e melhorias

O projeto ainda está em desenvolvimento e as próximas atualizações serão voltadas nas seguintes tarefas:

- [x] Configurar um projeto base
- [x] Preprar uma base para rodar a API
- [x] Definir um routeador de alta performance
- [x] Abstrair a infra para HTTP, Cache, Persistence e Log
- [ ] Fazer uma PoC com todos os serviços rodando
- [ ] Migrar a parte de persistência para uma estrutura mais formal
- [ ] Validar se actions como funções são mais performáticas que classes

## 💻 Pré-requisitos

Antes de começar, verifique se você atendeu aos seguintes requisitos:

* Você instalou a versão mais recente do Docker (que já vem com o plugin compose)
* Você tem um Javinha rodando para rodar os testes
* Você tem o ambiente preparado para rodar um makefile

## 🚀 Baixando o Twilight

Para começar é precisar fazer um clone deste repositório:

```
git clone git@github.com:opencodeco/twilight.git
```

Ou fazer download do zip
```
wget https://github.com/opencodeco/twilight/archive/refs/heads/main.zip
```

## ☕ Rodando o Twilight

Para por o projeto para rodar basta rodar o comando a serguir

```
make
```

Caso não tenha o make disponível rode na sequência:
- docker compose run --rm setup
- docker compose up

Para rodar o teste de stress utilize
```
make stress
```

Ou para ambientes Unix
```
sh ./gatling/run.sh
```

## 📫 Contribuindo para <nome_do_projeto>

Para contribuir com <nome_do_projeto>, siga estas etapas:

1. Bifurque este repositório.
2. Crie um branch: `git checkout -b <nome_branch>`.
3. Faça suas alterações e confirme-as: `git commit -m '<mensagem_commit>'`
4. Envie para o branch original: `git push origin <nome_do_projeto> / <local>`
5. Crie a solicitação de pull.

Como alternativa, consulte a documentação do GitHub em [como criar uma solicitação pull](https://help.github.com/en/github/collaborating-with-issues-and-pull-requests/creating-a-pull-request).

## 🤝 Colaboradores

Agradecemos às seguintes pessoas que contribuíram para este projeto:

<table>
  <tr>
    <td align="center">
      <a href="#">
        <img src="https://s.gravatar.com/avatar/47646f3d728d63e17a082deab0636c4b86c31c7a659b7042556a2df3714af9a5?s=80&r=pg" width="100px;" alt="William Correa"/><br>
        <sub>
          <b>William Correa</b>
        </sub>
      </a>
    </td>
  </tr>
</table>

## 📝 Licença

Esse projeto está sob licença. Veja o arquivo [LICENÇA](LICENSE.md) para mais detalhes.
