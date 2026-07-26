// data.jsx — sample content for Central de Artigos (IA / tech)
// Categories carry a color used by procedural thumbnails + tags.

const CATS = {
  ia:        { id: "ia",        name: "IA & Machine Learning", short: "IA",        ink: "#8b6dff", deep: "#1a1340", glyph: "∑", blurb: "Modelos, LLMs, agentes e a engenharia por trás da inteligência artificial aplicada." },
  eng:       { id: "eng",       name: "Engenharia de Software", short: "Engenharia", ink: "#22d3ee", deep: "#0a2b33", glyph: "{}", blurb: "Arquitetura, design de sistemas, qualidade e as práticas que sustentam software de produção." },
  cloud:     { id: "cloud",     name: "Cloud & DevOps",        short: "Cloud",     ink: "#34d399", deep: "#06281d", glyph: "☁", blurb: "Infraestrutura, observabilidade, CI/CD e operação de sistemas em escala." },
  backend:   { id: "backend",   name: ".NET & Backend",        short: "Backend",   ink: "#fbbf24", deep: "#2e2206", glyph: "λ", blurb: "APIs, performance, dados e o ecossistema .NET para back-end robusto." },
  front:     { id: "front",     name: "Frontend & Web",        short: "Frontend",  ink: "#fb7185", deep: "#33101a", glyph: "</>", blurb: "Interfaces, performance no browser, design systems e a plataforma web moderna." },
  carreira:  { id: "carreira",  name: "Carreira & Produtividade", short: "Carreira", ink: "#60a5fa", deep: "#0d1f3d", glyph: "↗", blurb: "Crescimento, liderança técnica e hábitos para quem constrói software." },
};
const CAT_ORDER = ["ia", "eng", "cloud", "backend", "front", "carreira"];

const AUTHORS = {
  ricky:  { id: "ricky",  name: "Ricardo Tavares",  role: "Founder & AI Engineer", av: "#7c5cff", initials: "RT", bio: "Fundador da Rickytech. Escreve sobre engenharia de IA que vai pra produção — dos prompts à observabilidade de modelos.", x: "@rickytavares", articles: 38, since: "2021" },
  lia:    { id: "lia",    name: "Lia Moreira",      role: "Staff Software Engineer", av: "#22d3ee", initials: "LM", bio: "Engenheira de plataforma. Apaixonada por sistemas distribuídos, DX e tornar o complexo legível.", x: "@liamoreira", articles: 24, since: "2022" },
  diego:  { id: "diego",  name: "Diego Sancho",     role: "Cloud Architect", av: "#34d399", initials: "DS", bio: "Arquiteto de nuvem. Ajuda times a operar sistemas confiáveis sem queimar o orçamento de infra.", x: "@diegosancho", articles: 19, since: "2022" },
  bia:    { id: "bia",    name: "Beatriz Nunes",    role: "Frontend Lead", av: "#fb7185", initials: "BN", bio: "Lead de frontend. Constrói design systems e defende performance percebida acima de tudo.", x: "@bianunes", articles: 27, since: "2021" },
};

// Reusable rich body used by the fully-written articles
const BODY_AGENTS = [
  { type: "p", html: "Construir um agente que <em>demonstra</em> bem é fácil. Construir um que sobrevive a usuários reais, dados sujos e APIs instáveis é outra história. Depois de colocar meia dúzia de agentes em produção, alguns padrões se repetem — e a maioria não tem a ver com o modelo em si." },
  { type: "h2", id: "contexto", text: "O contexto é o produto" },
  { type: "p", html: "O maior salto de qualidade quase nunca vem de trocar o modelo. Vem de <strong>controlar o que entra na janela de contexto</strong>. Um agente é tão bom quanto o material que você coloca diante dele no momento da decisão." },
  { type: "p", html: "Trate a montagem de contexto como um pipeline de dados de primeira classe: recuperação, ranqueamento, compressão e verificação. Cada etapa merece testes." },
  { type: "code", lang: "python", title: "context.py", tokens: [
    ["cc", "# Monte só o que importa para a decisão atual"], ["br"],
    ["cp", "def "], ["cf", "build_context"], ["", "(query, memory, tools):"], ["br"],
    ["", "    docs = retriever."], ["cf", "search"], ["", "(query, k="], ["cn", "8"], ["", ")"], ["br"],
    ["", "    docs = reranker."], ["cf", "rank"], ["", "(query, docs)["], ["cn", "0"], ["", ":"], ["cn", "3"], ["", "]"], ["br"],
    ["cp", "    return "], ["ct", "Context"], ["", "(docs=docs, memory=memory."], ["cf", "recent"], ["", "())"],
  ]},
  { type: "h2", id: "limites", text: "Dê limites firmes ao agente" },
  { type: "p", html: "Um agente sem limites é um gerador de incidentes. Defina <strong>orçamentos</strong> explícitos: máximo de passos, máximo de tokens, ferramentas permitidas por etapa. Quando o agente estoura o orçamento, ele deve falhar de forma previsível — não improvisar." },
  { type: "ul", items: [
    "Liste ferramentas com contratos rígidos de entrada e saída.",
    "Valide toda saída do modelo antes de executar efeitos colaterais.",
    "Registre cada passo com IDs de correlação para depurar a cadeia inteira.",
  ]},
  { type: "quote", text: "Um agente confiável não é o mais inteligente — é o mais observável.", pull: true },
  { type: "h2", id: "avaliacao", text: "Avaliação contínua, não pontual" },
  { type: "p", html: "Prompts quebram silenciosamente. Sem um conjunto de avaliações rodando em CI, você só descobre a regressão pelo ticket do cliente. Comece pequeno: 20 casos dourados que representam o caminho crítico, rodados a cada deploy." },
  { type: "h3", id: "metricas", text: "O que medir" },
  { type: "p", html: "Acurácia importa, mas latência de cauda e custo por tarefa decidem se o produto é viável. Acompanhe os três juntos — otimizar um isolado costuma piorar os outros." },
  { type: "p", html: "No fim, engenharia de agentes é engenharia de software com um componente probabilístico. As disciplinas antigas — testes, observabilidade, contratos — não saem de moda. Elas ficam <em>mais</em> importantes." },
];

const BODY_RAG = [
  { type: "p", html: "RAG virou sinônimo de \"colar busca vetorial num LLM\". Mas a maior parte dos ganhos — e das dores — está nos detalhes que ninguém mostra nos tutoriais." },
  { type: "h2", id: "chunking", text: "Chunking define o teto de qualidade" },
  { type: "p", html: "Se o pedaço recuperado não contém a resposta, nenhum modelo te salva. Chunking semântico, com sobreposição e metadados, supera o corte por tamanho fixo na quase totalidade dos casos reais." },
  { type: "code", lang: "typescript", title: "chunk.ts", tokens: [
    ["cp", "const "], ["", "chunks = splitter."], ["cf", "split"], ["", "(doc, {"], ["br"],
    ["", "  size: "], ["cn", "512"], ["", ", overlap: "], ["cn", "64"], ["", ","], ["br"],
    ["", "  keepHeadings: "], ["cn", "true"], ["", ",  "], ["cc", "// preserva contexto"], ["br"],
    ["", "})"],
  ]},
  { type: "h2", id: "reranking", text: "Reranking é o melhor custo-benefício" },
  { type: "p", html: "Recupere generosamente (top-k alto), depois reordene com um cross-encoder e fique só com os melhores. É a otimização com maior retorno por linha de código em todo o pipeline." },
  { type: "quote", text: "Recupere com recall, entregue com precisão.", pull: true },
  { type: "h2", id: "avaliar", text: "Meça antes de confiar" },
  { type: "p", html: "Sem métricas de recuperação (hit rate, MRR) você está ajustando às cegas. Monte um conjunto de perguntas com respostas conhecidas e trate a recuperação como um problema de ranking — porque é exatamente isso." },
];

const ARTICLES = [
  { slug: "agentes-producao", cat: "ia", author: "ricky", featured: true,
    title: "Agentes de IA em produção: o que ninguém te conta antes do primeiro incidente",
    lede: "Demos impressionam, produção castiga. Os padrões que separam um agente confiável de um gerador de incidentes — contexto, limites e observabilidade.",
    date: "2026-05-28", read: 9, tags: ["Agentes", "LLM", "Produção", "Observabilidade"], pop: 1, body: BODY_AGENTS },
  { slug: "rag-na-pratica", cat: "ia", author: "ricky",
    title: "RAG na prática: por que seu retrieval é ruim (e como consertar)",
    lede: "Chunking, reranking e avaliação — os três pilares que decidem se a sua busca aumentada por geração funciona ou alucina.",
    date: "2026-05-21", read: 7, tags: ["RAG", "Embeddings", "Busca vetorial"], pop: 2, body: BODY_RAG },
  { slug: "fine-tuning-vs-rag", cat: "ia", author: "lia",
    title: "Fine-tuning ou RAG? Um framework de decisão sem hype",
    lede: "Quando ajustar pesos vale a pena, quando recuperar contexto basta, e por que a resposta quase sempre é \"os dois, na ordem certa\".",
    date: "2026-05-14", read: 8, tags: ["Fine-tuning", "RAG", "Custos"], pop: 4 },
  { slug: "avaliando-llms", cat: "ia", author: "ricky",
    title: "Como avaliar LLMs de verdade: evals que rodam em CI",
    lede: "Pare de validar prompts no olho. Um guia para montar suites de avaliação automatizadas que pegam regressões antes do deploy.",
    date: "2026-05-06", read: 6, tags: ["Evals", "Qualidade", "CI/CD"], pop: 7 },

  { slug: "design-sistemas-legivel", cat: "eng", author: "lia", featured: true,
    title: "Design de sistemas legível: arquitetura que cabe na cabeça",
    lede: "Complexidade acidental mata produtividade. Princípios para desenhar sistemas que um novo dev entende em uma tarde, não em um trimestre.",
    date: "2026-05-26", read: 10, tags: ["Arquitetura", "Complexidade", "DX"], pop: 3 },
  { slug: "testes-que-importam", cat: "eng", author: "lia",
    title: "Testes que importam: cobertura é vaidade, confiança é métrica",
    lede: "Mais testes não significa mais segurança. Como escolher o que testar para dormir tranquilo nos deploys de sexta.",
    date: "2026-05-19", read: 7, tags: ["Testes", "Qualidade"], pop: 8 },
  { slug: "monolito-vs-micro", cat: "eng", author: "diego",
    title: "Monólito modular: o meio-termo que ninguém quer admitir que funciona",
    lede: "Antes de quebrar tudo em microsserviços, considere o monólito bem fatiado. Menos rede, menos dor, mais velocidade.",
    date: "2026-05-11", read: 9, tags: ["Arquitetura", "Microsserviços"], pop: 6 },

  { slug: "observabilidade-pratica", cat: "cloud", author: "diego", featured: true,
    title: "Observabilidade sem se afogar em dashboards",
    lede: "Logs, métricas e traces todo mundo tem. O que falta é saber qual pergunta cada um responde — e quando ligar para alguém.",
    date: "2026-05-24", read: 8, tags: ["Observabilidade", "SRE", "Traces"], pop: 5 },
  { slug: "kubernetes-suficiente", cat: "cloud", author: "diego",
    title: "Você (provavelmente) não precisa de Kubernetes ainda",
    lede: "Um teste honesto de quando a complexidade do k8s se paga — e o que usar antes de chegar lá.",
    date: "2026-05-16", read: 6, tags: ["Kubernetes", "Infra", "Custos"], pop: 9 },
  { slug: "custos-cloud", cat: "cloud", author: "diego",
    title: "FinOps na real: cortando 40% da conta de cloud sem demitir o desempenho",
    lede: "Onde o desperdício mora, como medir custo por feature e por que o maior vilão costuma ser o egress.",
    date: "2026-05-08", read: 7, tags: ["FinOps", "AWS", "Custos"], pop: 11 },

  { slug: "dotnet-performance", cat: "backend", author: "lia",
    title: "Performance em .NET: do alloc ao Span, sem mágica",
    lede: "Spans, pooling e o profiler como bússola. Otimizações de back-end que rendem latência menor sem reescrever tudo.",
    date: "2026-05-22", read: 9, tags: [".NET", "Performance", "C#"], pop: 10 },
  { slug: "apis-versionamento", cat: "backend", author: "diego",
    title: "Versionar APIs sem ódio: contratos que envelhecem bem",
    lede: "Estratégias de versionamento, deprecação gradual e por que quebrar o cliente é sempre culpa do servidor.",
    date: "2026-05-13", read: 6, tags: ["APIs", "Design", "Contratos"], pop: 12 },

  { slug: "performance-percebida", cat: "front", author: "bia", featured: true,
    title: "Performance percebida: rápido é o que o usuário sente, não o que o gráfico diz",
    lede: "Skeleton screens, optimistic UI e o segredo de parecer instantâneo mesmo quando a rede não colabora.",
    date: "2026-05-27", read: 7, tags: ["Performance", "UX", "Web"], pop: 13 },
  { slug: "design-system-escala", cat: "front", author: "bia",
    title: "Design system que escala: tokens, não exceções",
    lede: "Como manter consistência quando 40 devs mexem na UI — e por que a documentação é parte do componente.",
    date: "2026-05-18", read: 8, tags: ["Design System", "Tokens", "DX"], pop: 14 },
  { slug: "css-moderno", cat: "front", author: "bia",
    title: "CSS moderno tornou metade do seu JavaScript obsoleto",
    lede: "Container queries, :has(), subgrid e cascade layers. O que dá pra apagar do bundle hoje.",
    date: "2026-05-09", read: 6, tags: ["CSS", "Web", "Plataforma"], pop: 15 },

  { slug: "ic-tech-lead", cat: "carreira", author: "lia",
    title: "De IC a tech lead sem virar gerente que você odiava",
    lede: "Liderança técnica é multiplicar, não controlar. O que muda no seu dia — e o que você precisa parar de fazer.",
    date: "2026-05-20", read: 7, tags: ["Carreira", "Liderança"], pop: 16 },
  { slug: "foco-profundo", cat: "carreira", author: "ricky",
    title: "Trabalho profundo num mundo de notificações: um sistema, não força de vontade",
    lede: "Como proteger blocos de foco quando todo mundo espera resposta em cinco minutos.",
    date: "2026-05-12", read: 5, tags: ["Produtividade", "Foco"], pop: 17 },
];

// give every article a default body if none authored
ARTICLES.forEach(a => { if (!a.body) a.body = BODY_AGENTS; });

function fmtDate(iso) {
  const d = new Date(iso + "T00:00:00");
  return d.toLocaleDateString("pt-BR", { day: "2-digit", month: "short", year: "numeric" }).replace(".", "");
}
function relDate(iso) {
  const d = new Date(iso + "T00:00:00"), now = new Date("2026-06-03");
  const days = Math.round((now - d) / 86400000);
  if (days <= 0) return "hoje";
  if (days === 1) return "ontem";
  if (days < 7) return `há ${days} dias`;
  if (days < 30) return `há ${Math.floor(days/7)} sem`;
  return fmtDate(iso);
}
function catCount(catId) { return ARTICLES.filter(a => a.cat === catId).length; }

Object.assign(window, { CATS, CAT_ORDER, AUTHORS, ARTICLES, fmtDate, relDate, catCount });
