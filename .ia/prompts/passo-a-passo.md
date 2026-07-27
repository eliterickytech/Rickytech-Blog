# Passo a passo — capa com fundo de IA (Gemini/Nano Banana) + branding

Fluxo para criar a capa de um post usando **imagem de IA de verdade** com o que
você já paga (**Google AI Pro**, pelo app Gemini), e deixar o branding sempre
nítido (aplicado por código pela skill `publicar-post`).

> Por que assim: o Nano Banana pela **CLI/API** exige billing (cota 0 hoje). Já
> o **app Gemini** gera imagem no seu plano Pro. Então: gera no app → baixa →
> a skill compõe o branding + publica.

## 1. Gerar o fundo no app Gemini
1. Abra **https://gemini.google.com** (logado na conta com o **Pro**).
2. Abra o prompt em [`capa-fundo-nanobanana.md`](capa-fundo-nanobanana.md).
3. Copie o prompt (versão inglês costuma sair melhor), troque **`{TEMA}`** pelo
   assunto do post e **`{MOTIVO}`** pela dica da categoria (tabela no arquivo).
4. Envie. Peça **variações** se quiser e escolha a com **mais espaço vazio à
   esquerda** (é onde entra o título).

## 2. Baixar a imagem
5. Baixe o PNG na maior resolução (ideal **16:9**).
6. Salve em **`.ia/capas/`** com um nome que lembre o post, ex.:
   `.ia/capas/claude-fable-5.png`.

## 3. Publicar (a skill aplica o branding e sobe)
7. No Claude Code, é só pedir — algo como:
   *"publica o post sobre X usando o fundo `.ia/capas/claude-fable-5.png`"* —
   que eu rodo a skill `publicar-post` com esse fundo.

   Ou rode direto (rascunho primeiro):
   ```
   mcp-wordpress\.venv\Scripts\python.exe .claude\skills\publicar-post\scripts\publicar.py ^
     --titulo "TÍTULO DO POST" ^
     --corpo "CAMINHO\corpo.html" ^
     --categoria ia ^
     --resumo "Linha de chamada." ^
     --fundo ".ia\capas\claude-fable-5.png"
   ```
   O `--fundo` usa a imagem da IA como fundo e **desenha o branding por cima**
   (logo, pill de categoria, título, rodapé). O resultado é a capa 1200×675.

8. Revise o **preview** e, com o OK, publique (e fixe como destaque se quiser):
   ```
   mcp-wordpress\.venv\Scripts\python.exe .claude\skills\publicar-post\scripts\publicar.py ^
     --update ID --status publish --destaque
   ```

## Variações úteis
- **Só ver a capa antes** (sem publicar):
  ```
  mcp-wordpress\.venv\Scripts\python.exe .claude\skills\publicar-post\scripts\gerar_capa.py ^
    --titulo "TÍTULO" --eyebrow IA --fundo ".ia\capas\arquivo.png" --saida ".ia\capas\preview.png"
  ```
- **Sem fundo de IA** (fundo por código, grátis): é o padrão — não passe `--fundo`.
- **Capa 100% pronta** (já com texto, feita fora): use `--capa arquivo.png` no
  `publicar.py` (aí não desenhamos branding por cima).

## Quando quiser 100% automático (opcional)
Habilite **billing numa API key do Google AI Studio** e me avise. Aí a skill
passa a **gerar o fundo por IA sozinha** (sem o passo manual do app), mantendo o
mesmo branding por cima. Custo ~centavos por imagem.
