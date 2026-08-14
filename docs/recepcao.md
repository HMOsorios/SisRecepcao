# Sistema de Gestão de Recepção e Atendimento - SMS (v2.0)
## Secretaria Municipal de Saúde

---
## Atenção
* Pasta novosga-23 - Gov baixada do GitHub com programa de Recepçção e Fila de atendimento 'SGA 23' do Gov Federal.
Faça uma analise de como poderiamos utiliza-lo no desenvolvimento do nosso SisRecepção!recepcao.md e 

## 1. Visão Geral e Estrutura Organizacional

O atendimento na sede da Secretaria Municipal de Saúde (SMS) é a porta de entrada para cidadãos, servidores públicos, fornecedores e prestadores de serviço. Por lidar com demandas humanas sensíveis, regulatórias e administrativas, a recepção da SMS exige organização impecável, empatia, agilidade e clareza na triagem.

### 1.1. Boas Práticas na Recepção
* **Acolhimento Humanizado:** Atendimento cordial, empático e com escuta ativa.
* **Triagem Eficiente:** Identificação rápida do motivo da visita para direcionamento correto.
* **Acessibilidade e Prioridade:** Respeito rigoroso às prioridades legais (idosos, gestantes, pessoas com deficiência e pessoas no espectro autista - TEA).
* **Sistema de Tickets/Senhas:** Emissão de senhas categorizadas (ex: *Atendimento Geral*, *Entrega de Documentos*, *RH*, *Pagamentos*, *Prioritário Legal*) para manter a ordem e gerar métricas.
* **Painel de Chamada:** Painéis visuais e sonoros organizados para chamada de senhas, garantindo transparência na fila.
* **Identificação e Crachás:** Registro de visitantes por nome e documento, com entrega de crachá temporário para segurança do prédio.

---

### 1.2. Mapeamento de Salas e Setores da SMS

#### Gestão Superior e Controle
* **Gabinete:** Atendimento ao Secretário(a) de Saúde, reuniões estratégicas, recepção de autoridades e demandas institucionais prioritárias.
* **Ouvidoria:** Canal direto com o cidadão para registrar elogios, reclamações, denúncias, solicitações ou sugestões sobre os serviços de saúde.
* **Auditoria:** Setor responsável pela fiscalização, controle interno e avaliação da aplicação dos recursos e qualidade dos serviços.

#### Regulação, Avaliação e Redes Assistenciais
* **Controle e Avaliações:** Análise e regulação de exames, consultas especializadas e procedimentos. Acompanha metas e prazos assistenciais.
* **Diretoria de Atenção Básica (DAB):** Gestão das Unidades Básicas de Saúde (UBS/USF), programas da família, vacinação e ações preventivas na comunidade.
* **Diretoria de Média e Alta Complexidade (MAC):** Gestão da atenção especializada, laboratórios, centros de especialidades, UPAs e leitos hospitalares.
* **Saúde da Mulher:** Coordenação de programas específicos voltados à saúde feminina, pré-natal, rastreamento de câncer e planejamento familiar.
* **Doenças Crônicas:** Acompanhamento de programas para pacientes com condições contínuas, como Diabetes, Hipertensão e Doenças Ocupacionais.
* **Extra Muro:** Ações de saúde fora das unidades físicas, como feiras de saúde, campanhas itinerantes e busca ativa em comunidades.

#### Gestão Administrativa, Financeira e Logística
* **Diretoria Administrativa:** Gestão operacional do prédio, patrimônio, suprimentos gerais, manutenção e infraestrutura.
* **Diretoria Financeira:** Planejamento orçamentário, gestão de fundos de saúde e prestação de contas dos recursos públicos.
* **Pagamentos:** Processamento, conferência e liquidação de notas fiscais de fornecedores, prestadores de serviços e convênios.
* **Recursos Humanos (RH):** Atendimento focado nos servidores da saúde (folha de pagamento, férias, licenças, processos seletivos e posse).
* **Planejamento:** Elaboração do Plano Municipal de Saúde, Relatório Anual de Gestão (RAG) e acompanhamento de indicadores.
* **Transporte:** Gestão da frota da SMS, agendamento de TFD (Tratamento Fora do Domicílio), remoções e deslocamento de equipes.

---

## 2. Especificação Funcional do Sistema de Recepção

### 2.1. Módulo de Totem de Autoatendimento e Triagem
* **Categorização Clara de Serviços:** Atendimento Geral, Entrega de Documentos/Protocolo, Servidores (RH/Folha), Fornecedores (Financeiro/Pagamentos) e Prioritário Legal.
* **Acessibilidade:** Interface touchscreen intuitiva com suporte a leitura em áudio, ajuste de contraste e altura.
* **Emissão de Ticket:** Impressão de senha com código, categoria, data/hora, estimativa de espera e QR Code para acompanhamento pelo smartphone.

### 2.2. Módulo de Chamada e Sinalização (Painéis de TV)
* **Integração com Smart TVs:** Exibição da chamada no painel principal da recepção.
* **Alertas Sonoros (TTS):** Sinal sonoro agradável e síntese de voz nativa/cloud anunciando a senha e a sala/guichê de destino.
* **Multimídia Integrada:** Divisão de tela para exibição de campanhas de saúde pública (vacinação, prevenção) e informes institucionais.

### 2.3. Módulo de Gestão de Crachás e Controle de Acesso
* **Cadastro Rápido:** Leitura de documento (CPF/RG) por OCR/scanner, captura de foto por webcam e vínculo com a sala de destino.
* **Etiqueta/Crachá Temporário:** Impressão com nome, foto, CPF mascarado e sala autorizada.
* **Notificação Automática:** Alerta instantâneo (pop-up/chat interno) para o setor informando a chegada do visitante.

### 2.4. Painel do Atendente e Gestão Operacional
* **Gestão de Filas:** Botão simplificado para chamada com alternância automática entre senhas prioritárias e convencionais.
* **Redirecionamento Interno:** Transferência digital da senha entre setores sem necessidade de novo ticket.
* **Status e Agendas:** Controle de status (*Em atendimento*, *Pausa*, *Ausente*) e consulta à disponibilidade dos setores.

### 2.5. Módulo de BI, Relatórios e Segurança (LGPD)
* **Dashboards em Tempo Real:** Indicadores de tempo médio de espera, tempo de atendimento, volume por setor e horários de pico.
* **Segurança e LGPD:** Mascaramento de dados em telas públicas, criptografia de dados pessoais e rotina automatizada de expurgo/anonimização.
* **Operação Resiliente:** Funcionamento off-line/local em caso de instabilidade na conexão de internet.

### 2.6. Fluxo Detalhado: Notificação ao Setor e Liberação de Acesso

Este fluxo integra os módulos 2.3 (Crachás) e 2.4 (Painel do Atendente) com o painel de TV da recepção (2.2), usando os mecanismos do NovoSGA já validados na Seção 5.

**Modelagem:** cada setor do mapeamento da Seção 1.2 (Gabinete, Ouvidoria, RH, Financeiro etc.) é representado como um `Departamento` do NovoSGA, todos pertencentes a uma única `Unidade` — "Sede SMS". Essa escolha preserva o significado original de `Unidade` como unidade de saúde física (relevante caso o SisRecepção seja futuramente estendido a UBS/USF geridas pela DAB) e usa `Departamento` para a granularidade interna do prédio-sede.

**Passo a passo:**

1. **Abertura da visita (Recepção → Crachá):** o atendente da recepção registra o visitante (OCR/CPF, foto, setor de destino) no módulo 2.3. O SisRecepção cria um `Atendimento` no NovoSGA vinculado ao `Servico`/`Departamento` de destino, via `POST /api/distribui`, com status inicial `SENHA_EMITIDA`.
2. **Aviso em tempo real no setor:** a criação do `Atendimento` dispara automaticamente os eventos Mercure `notificaFilaUnidade` (`/unidades/{id}/fila`) e `notificaAtendimento`. O console do setor (módulo 2.4) mantém uma conexão SSE aberta nesse tópico e filtra localmente pelo `departamento_id` correspondente — ao chegar um evento do seu setor, exibe o pop-up/alerta sonoro imediatamente ("Notificação Automática" da Seção 2.3).
3. **Liberação de acesso pelo setor:** o servidor do setor, ao ver o visitante na fila, clica em "Liberar/Chamar" no seu terminal. Isso aciona `POST /api/atendimentos/{id}/chamar` (opcionalmente seguido de `/iniciar` quando o atendimento de fato começa), avançando o status para `CHAMADO_PELA_MESA`.
4. **Atualização automática do telão:** a mudança de status publica o evento `notificaPainel` (`/unidades/{id}/painel`). O painel de TV da recepção (script `painel.js`, já existente no NovoSGA) está inscrito nesse tópico via `EventSource` e se atualiza sozinho — sem polling manual —, exibindo "Fulano, dirija-se a [Setor]", com fallback automático para polling a cada 8s caso o SSE fique indisponível.

**Observação de escala:** como toda a fila da Sede SMS roda sob uma única `Unidade`, todos os consoles de setor compartilham o mesmo canal Mercure e fazem o filtro por `departamento_id` no cliente. O volume de eventos de uma recepção predial é baixo, então isso não representa gargalo — é a mesma arquitetura de tópicos que o NovoSGA já usa em produção para múltiplos guichês dentro de uma unidade.

---

## 3. Arquitetura Tecnológica e Bancos de Dados Suportados

A arquitetura do sistema é flexível e permite a utilização tanto de **MySQL / MariaDB** quanto de **PostgreSQL**, conforme a infraestrutura e a experiência da equipe de TI do município.

### 3.1. Matriz de Tecnologias Recomendadas

> **Atualizado pela Seção 8.6:** **PHP 8.3 + Laravel** como stack única de todo o SisRecepção, substituindo a proposta polyglot original (Node/FastAPI + React/Next.js), para consistência com o padrão já adotado no GNIRSaude.
> **Refinado pela avaliação de operacionalidade (Seção 7.5):** essa stack é entregue como **dois apps Laravel independentes** — `sisrecepcao-atendimento` (linha de frente: totem, painel TV, crachá, console do atendente) e `sisrecepcao-admin` (portal institucional, Diretoria Administrativa, Configurações/auto-deploy) — para que uma atualização do painel administrativo nunca derrube a emissão de senha na recepção.

| Camada / Módulo | Tecnologias Recomendadas | Justificativa / Benefícios |
| :--- | :--- | :--- |
| **Front-End (Totem & TV)** | Blade + JavaScript vanilla (Modo Kiosk PWA — `manifest.json` + service worker), servido por `sisrecepcao-atendimento` | Mesma convenção do GNIRSaude; interface leve, sem overhead de SPA framework, com funcionamento off-line via cache de assets. Painel TV e console do atendente recebem atualização direto do Mercure do NovoSGA via `EventSource` no navegador (Seção 5.1/2.6) — não dependem de round-trip contínuo ao Laravel. |
| **Front-End (Painel & BI / Portal Institucional)** | Blade + TailwindCSS (via Vite), servido por `sisrecepcao-admin` | Carregamento rápido, responsivo, mesma stack de build; isolado do app de atendimento (Seção 7.5). |
| **Back-End e Comunicação** | **PHP 8.3+ / Laravel**, dois apps (`sisrecepcao-atendimento` e `sisrecepcao-admin`) — tempo real via consumo direto do **Mercure (SSE)** do NovoSGA (Seção 5.1) no navegador; Laravel Reverb como alternativa caso o SisRecepção precise de canal de eventos próprio. | Stack única para todo o sistema (Seção 8.6), com isolamento de disponibilidade entre operação e administração (Seção 7.5); sem necessidade de WebSocket próprio, já que o NovoSGA expõe SSE nativamente. |
| **Banco de Dados Relacional** | **MySQL 8.0+ / MariaDB 10.6+** (confirmado na Seção 5.3) — mesmo servidor, schema `sisrecepcao_db` compartilhado pelos dois apps | Suporte completo via Eloquent/Doctrine (NovoSGA), mecanismo InnoDB, transações ACID, facilidade de administração pela TI municipal. |
| **Cache em Memória (Filas)** | **Redis** (via driver de cache/queue do Laravel) | Cache de leitura/estimativas do lado do SisRecepção — o NovoSGA em si não usa Redis (Seção 5.2). |
| **Síntese de Voz (TTS)** | Web Speech API / AWS Polly / Google Cloud TTS | Chamada por áudio clara, natural e acessível na recepção. |
| **Infraestrutura / Acesso** | Docker + **Coolify** (dois serviços/deploys distintos, Seção 7.5/8.9) + Keycloak (OAuth 2.0/OIDC, Seção 5.4) | Implantação em servidor próprio via Coolify; auto-atualização (GitHub/migrações) restrita ao `sisrecepcao-admin`; SSO central via Keycloak para ambos. |

---

## 4. Estratégia de Integração com o e-SGA Livre (DATAPREV / Governo Federal)

O **e-SGA (Sistema de Gerenciamento do Atendimento)** é um software público de código aberto mantido pela comunidade do Governo Federal. A integração entre o Sistema da SMS e o e-SGA traz alta eficiência e economia de recursos públicos.

### 4.1. Modelos de Integração Possíveis

1. **e-SGA como Back-End Core (Motor de Filas):**
   * O e-SGA v2.0+ gerencia as regras de ordenação de filas, prioridades e persistência.
   * O novo sistema da SMS atua como a camada moderna de frontend (Totem Touchscreen PWA, Painel TV estilizado com a marca da SMS, Módulo de Crachás com OCR e Notificações ao Gabinete).
   * **Comunicação:** Realizada via APIs RESTful nativas do e-SGA.

2. **Integração no Nível de Banco de Dados / Filas Compartilhadas:**
   * Utilizado para instalações existentes do e-SGA. Os tickets emitidos na recepção são gravados diretamente na base do e-SGA (MySQL/PostgreSQL) ou no Redis compartilhado.

3. **Sincronização Estatística e Prestação de Contas:**
   * O sistema da SMS roda de forma autônoma e envia dados estatísticos de atendimento de forma assíncrona para a base do e-SGA municipal/federal para consolidated de relatórios públicos.

### 4.2. Vantagens da Integração
* **Economia do Erário Público:** Reuso da lógica regulatória e de filas já consolidada do Governo Federal.
* **Conformidade com a Legislação do SUS:** Respeito automático às regras de prioridade nacionais.
* **Sem Lock-in de Fornecedor:** Código 100% aberto e customizável pela própria TI do município.

---

## 5. Análise de Viabilidade: NovoSGA 2.3 e Confirmação da Base MySQL

Esta seção documenta a análise técnica do código-fonte real fornecido em `docs/novosga-2.3 - Gov` (NovoSGA, mantido por Mangati), avaliando sua adequação como motor de filas do SisRecepção e confirmando a viabilidade da tecnologia e do banco de dados **MySQL** definidos como padrão para o projeto.

### 5.1. O que o NovoSGA 2.3 Realmente É

Diferente do SGA Livre clássico (DATAPREV), esta versão é uma reescrita moderna: **Symfony 7.4 / PHP 8.2**, organizada como monólito modular (bundles `novosga/core`, `attendance-bundle`, `panel-bundle`, `triage-bundle` etc.), com:

* **API REST real** em `/api` (CRUD completo + ações de negócio: `distribui`, `chamar`, `iniciar`, `encerrar`, `redirecionar`), autenticada via **OAuth2 próprio** (`league/oauth2-server-bundle`) — não Keycloak.
* **Tempo real via Mercure (SSE)**, não WebSocket — é o mecanismo que já alimenta o painel de TV atual do NovoSGA (`notificaPainel`, `notificaFilaUnidade`).
* Domínio já maduro para o caso de uso da SMS: `Unidade` (multiunidade, com timezone própria), `Servico`/`ServicoUnidade` (fila por serviço × unidade, com prefixo, faixa de numeração e teto diário), `Prioridade` (peso + cor configuráveis — cobre idoso/gestante/PCD/TEA sem código novo), algoritmo de ordenação com **aging** configurável (evita fome de senha normal), e `Lotacao` (vínculo servidor↔unidade).
* **Não existe totem/kiosk PWA de autoatendimento** nem módulo de crachá/OCR no NovoSGA — esses módulos (2.1 e 2.3 desta especificação) são 100% novos em qualquer cenário de integração.
* Já existe **webhook de saída** (`ticket.created`, `ticket.called`, `ticket.finished`...) pronto para sincronização estatística assíncrona (Modelo 3 da seção 4.1).

### 5.2. Avaliação da Matriz Tecnológica (Seção 3) à Luz do NovoSGA

A matriz de tecnologias da Seção 3.1 permanece válida com um ajuste de escopo:

| Camada | Validação frente ao NovoSGA |
| :--- | :--- |
| **Front-End (Totem & TV)** | Confirmado como desenvolvimento novo — o NovoSGA não possui equivalente (frontend dele é Twig + Vue 2 sem build, apenas para o back-office e painel). |
| **Back-End e Comunicação** | O Laravel do SisRecepção (`sisrecepcao-atendimento`, Seção 7.1) **não substitui** o back-end do NovoSGA; atua como camada de orquestração que consome a API REST e o canal Mercure (SSE) já existentes, evitando reescrever a máquina de estados de atendimento. |
| **Cache em Memória (Filas)** | O NovoSGA **não usa Redis** — toda fila vive no PostgreSQL/MySQL via Doctrine. Se o Redis da Seção 3.1 for mantido, ele passa a ser um componente exclusivo do SisRecepção (cache de leitura/estimativas), não um recurso compartilhado com o NovoSGA. |
| **Infraestrutura** | O NovoSGA roda em **FrankenPHP** (Caddy + PHP, não PHP-FPM/Nginx clássico) via Docker, com um processo worker adicional (`messenger:consume`) para jobs assíncronos (ex.: disparo de webhooks). Isso deve ser somado à infraestrutura Docker/Coolify já prevista (Seção 8.9) — o NovoSGA roda como um serviço a mais no mesmo servidor próprio, não em Kubernetes. |

### 5.3. Confirmação do Banco de Dados: MySQL

O código foi inspecionado especificamente para validar o MySQL como escolha de banco (`migrations/sql/*.mysql.sql`, `config/packages/doctrine.yaml`, `.env`):

* **Suporte oficial e paritário:** cada migração do NovoSGA existe em par (`*.mysql.sql` e `*.postgres.sql`), incluindo o schema inicial (`v1__init.mysql.sql`) e as evoluções mais recentes (OAuth2, webhooks, painel embutido). Não há *feature gap* entre os dois engines — o time do NovoSGA mantém MySQL como cidadão de primeira classe, não como opção secundária.
* **Schema MySQL é convencional e compatível com a TI municipal:** tabelas `InnoDB`, `CHARSET=utf8mb4` / `COLLATE=utf8mb4_unicode_ci`, chaves primárias `AUTO_INCREMENT` padrão (estratégia Doctrine `GeneratedValue(strategy: 'IDENTITY')` — mapeia nativamente para `AUTO_INCREMENT` no MySQL, sem overhead de emulação de sequência). Nenhum recurso exclusivo de PostgreSQL (arrays, JSONB, extensões) é usado nas tabelas centrais de fila/atendimento.
* **Versão mínima confirmada:** `.env` traz como alternativa comentada `mysql://...?serverVersion=8.0.32` ou `MariaDB 10.11.2`, alinhado com o que já está definido na Seção 3.1 (**MySQL 8.0+ / MariaDB 10.6+**) — necessário para suporte nativo a colunas `JSON` (usadas nas tabelas `*_metadata`) e `utf8mb4` completo.
* **Ponto de atenção:** o `compose.yaml` e o `.env` de exemplo do repositório vêm configurados por padrão para **PostgreSQL 16**, ou seja, o ambiente de desenvolvimento/CI do projeto upstream é testado primariamente em Postgres. Adotar MySQL exige apenas trocar `DATABASE_URL` e o serviço de banco no compose — nenhuma mudança de mapeamento ORM é necessária —, mas recomenda-se rodar a suíte de testes (`phpunit.dist.xml`) contra MySQL/MariaDB antes de ir a produção, já que esse caminho é menos exercitado pela comunidade no dia a dia.
* **Sem dependência de Redis:** o `Messenger` (fila de jobs assíncronos, ex.: webhooks) usa o próprio banco (`doctrine://default`) por padrão, então adotar MySQL não introduz necessidade de infraestrutura extra além do banco relacional.

### 5.4. Recomendação Final

1. **Motor de filas:** adotar o **Modelo 1** (Seção 4.1) — NovoSGA como back-end core via API REST + Mercure — em vez dos Modelos 2 ou 3 isoladamente. Reescrever a lógica de priorização/aging e a máquina de estados do `AtendimentoService` do zero (Modelo 2) não se justifica diante de uma implementação já madura.
2. **Banco de dados:** **MySQL 8.0+** confirmado como viável e recomendado, sem ressalvas estruturais — schema paritário, sem features Postgres-only em uso, alinhado à experiência da TI municipal (conforme já justificado na Seção 3.1). Único cuidado prático: validar a suíte de testes do NovoSGA contra MySQL antes do go-live, por ser o caminho menos testado upstream.
3. **Autenticação (decidido):** **Keycloak como IdP central** do SisRecepção — SSO único para todos os módulos (console do atendente, BI, admin), alinhado à Seção 3.1 e preparado para futura integração de SSO com outros sistemas da SMS (ex.: GNIRSaude). Como o NovoSGA emite seus próprios tokens OAuth2 e não delega a um IdP externo (não existe ponte OIDC/Keycloak pronta nele hoje), o app `sisrecepcao-atendimento` (Seção 7.1) autentica suas chamadas à API do NovoSGA com uma **credencial de conta de serviço** própria (não a credencial do atendente). **Mitigação de auditoria:** como isso faria todo atendimento aparecer no NovoSGA como executado pela conta de serviço, o app registra o atendente real (id do Keycloak) na tabela de extensão `AtendimentoMeta` do próprio NovoSGA (namespace `sisrecepcao`, ex.: `atendente_keycloak_id`) a cada ação, preservando a rastreabilidade sem exigir mudanças no core do NovoSGA.
4. **Escopo 100% novo, independente do modelo escolhido:** Totem de autoatendimento (2.1), módulo de crachá/OCR (2.3) e a camada de BI/LGPD (2.5) não têm equivalente no NovoSGA e devem ser construídos do zero como especificado nas seções 2.1, 2.3 e 2.5.

---

## 6. Ferramentas de Desenvolvimento

### 6.1. DBeaver — Cliente SQL Padrão da Equipe

O código-fonte fornecido em `docs/dbeaver-devel` corresponde ao **DBeaver Community Edition** (licença Apache 2.0), cliente universal de banco de dados open-source (Java, Eclipse RCP/OSGi), com suporte nativo a mais de 100 drivers, incluindo **MySQL, MariaDB e PostgreSQL** out-of-the-box.

* **Natureza da ferramenta:** DBeaver é um aplicativo **desktop** de administração/consulta de banco de dados (editor SQL, editor de dados, diagramas ER, import/export, planos de execução, dashboards). **Não é um componente de runtime** do SisRecepção — não entra na arquitetura descrita na Seção 3 nem se comunica com o NovoSGA em produção. É uma ferramenta de trabalho para quem desenvolve e administra o banco.
* **Forma de uso recomendada:** utilizar o **binário oficial pré-compilado** (`dbeaver.io/download`), não compilar a partir do código-fonte em `docs/dbeaver-devel` — não há necessidade de customização que justifique build próprio, e o projeto upstream já distribui instaladores para Windows/Linux/macOS com JRE (OpenJDK 21) embutido.
* **Aplicação prática no SisRecepção:**
  * Modelagem e inspeção do schema **MySQL** confirmado na Seção 5.3, incluindo engenharia reversa do schema do NovoSGA (`atendimentos`, `unidades`, `servicos_unidades`, `prioridades` etc.) via diagrama ER, para apoiar o design das tabelas próprias do SisRecepção (crachás, visitantes, BI).
  * Consulta e depuração de dados durante o desenvolvimento (ex.: verificar eventos gravados pelo `AtendimentoService`, validar o fluxo da Seção 2.6).
  * Export/import de dados em migrações e cargas iniciais (unidades, serviços, prioridades legais pré-cadastradas).

### 6.2. CloudBeaver — Acesso Web à Base para a TI Municipal

O código-fonte fornecido em `docs/cloudbeaver-devel` confirma o **CloudBeaver Community** como a versão web-based do DBeaver (licença Apache 2.0): servidor em **Java** + interface web em **TypeScript/React**, mesmo motor de conexão do DBeaver, mas exposta via navegador em vez de aplicativo desktop. Trata-se de um projeto ativamente mantido — o `CHANGELOG` embutido no `README.md` mostra release **26.1.4 (2026-08-03)**, incluindo correções de segurança recentes em drivers (PostgreSQL JDBC atualizado para 42.7.13, entre outras) e suporte nativo a **MySQL, PostgreSQL, MariaDB, SQLite, Firebird, ClickHouse**, entre outros.

* **Uso pretendido:** painel de administração/consulta do banco **MySQL** do SisRecepção acessível pela TI municipal (ou por servidores autorizados) direto do navegador, sem precisar instalar o cliente desktop (DBeaver) em cada máquina — útil em ambientes com restrição de instalação local ou acesso remoto.
* **Forma de uso recomendada:** assim como o DBeaver (Seção 6.1), usar a **imagem Docker oficial** (`docker/` em `deploy/`, publicada em `hub.docker.com/r/dbeaver/cloudbeaver`) em vez de compilar a partir de `docs/cloudbeaver-devel` — não há necessidade de build próprio para o caso de uso previsto.
* **Diferença de arquitetura frente ao DBeaver:** CloudBeaver roda como um **serviço próprio** (servidor + interface web), então, ao contrário do DBeaver desktop (Seção 6.1), ele passa a ser um componente adicional de infraestrutura a implantar (Docker) e proteger com controle de acesso — deve ser tratado com o mesmo rigor de segurança/LGPD já previsto na Seção 2.5 (mascaramento de dados pessoais, controle de quem pode consultar `clientes`/dados de visitantes). A versão atual já suporta autenticação via reverse proxy com auto-criação de usuários, relevante para integrar com o mesmo IdP (Keycloak) previsto na Seção 3.1.
* **Recurso adicional relevante:** a versão analisada inclui um AI Chat integrado ao editor SQL (geração/correção de queries via OpenAI ou Copilot, configurável e desativável no servidor) — não essencial ao SisRecepção, mas disponível caso a equipe de TI queira usá-lo.

---

## 7. Estrutura do Sistema

Consolidação de todas as decisões das seções anteriores (2, 3, 5, 6 e 8) em uma estrutura de projeto concreta. **Proposta de arquitetura — pastas ainda não criadas no repositório**, que hoje contém apenas `docs/`. **Revisado pela Seção 7.5:** dois apps Laravel independentes, por avaliação de operacionalidade, no lugar do monolito único inicialmente registrado.

### 7.1. Visão Geral da Arquitetura

O SisRecepção é entregue como **dois apps Laravel (PHP 8.3+) independentes**, um único repositório, dois deploys separados no Coolify:

* **`sisrecepcao-atendimento`** — linha de frente: totem (2.1), painel TV (2.2), crachá (2.3), console do atendente (2.4). Consome o **NovoSGA** como motor de filas (Modelo 1, Seção 4.1/5.4) via API REST + Mercure (SSE).
* **`sisrecepcao-admin`** — portal institucional (Seção 8): perfis, painel da Diretoria Administrativa, BI/relatórios (2.5), painel de Configurações/auto-atualização.

Ambos compartilham o mesmo servidor **MySQL** (`sisrecepcao_db`) e o mesmo realm **Keycloak** (Seção 5.4/8.6) para autenticação — um atendente logado em um dos apps usa a mesma identidade no outro.

```text
Totem PWA ──┐
Painel TV ──┤
Console Setor ──┼──► sisrecepcao-atendimento (Laravel) ──► API REST + Mercure (NovoSGA) ──► MySQL (novosga_db)
                │             │
                │             └──► MySQL (sisrecepcao_db) — crachás, visitantes, notificações
                │
BI/Diretoria ───┼──► sisrecepcao-admin (Laravel) ──► MySQL (sisrecepcao_db) — config, perfis, auditoria LGPD
Config./Deploy ─┘             │
                               └──► GitHub (pull) + Coolify (deploy/migrações) — Seção 8.4/8.9

                    Keycloak (OAuth2/OIDC) — IdP único para os dois apps (Seção 5.4/8.6)
```

### 7.2. Árvore de Diretórios Proposta

```text
sisrecepcao/
├── apps/
│   ├── atendimento/                    # sisrecepcao-atendimento — deploy Coolify #1
│   │   ├── app/
│   │   │   ├── Http/Controllers/
│   │   │   │   ├── Totem/              # Módulo 2.1 — kiosk PWA, touchscreen, emissão de senha
│   │   │   │   ├── PainelTv/           # Módulo 2.2 — painel de chamada (Smart TV), TTS
│   │   │   │   ├── Cracha/             # Módulo 2.3 — cadastro de visitante, OCR/webcam, crachá
│   │   │   │   └── Atendente/          # Módulo 2.4 — console do setor (por Departamento, Seção 2.6)
│   │   │   ├── Services/
│   │   │   │   ├── Novosga/            # Cliente da API REST + assinante Mercure do NovoSGA (Seção 5.1/2.6)
│   │   │   │   ├── Cracha/             # Regras de cadastro, OCR, geração de crachá temporário
│   │   │   │   ├── Notificacoes/       # Ponte Mercure → pop-up/som no console do setor (Seção 2.6, passo 2)
│   │   │   │   └── Protocolo/          # Ponto de extensão p/ SEI — interface, sem implementação (Seção 7.4, item 1)
│   │   │   ├── Models/                 # Eloquent — Visitante, Cracha (sisrecepcao_db)
│   │   │   └── Auth/                   # Cliente OIDC do Keycloak (Seção 5.4/8.6)
│   │   ├── database/migrations/        # Tabelas próprias: crachás, visitantes, notificações
│   │   ├── resources/
│   │   │   ├── views/{totem,painel-tv,atendente}/   # Blade
│   │   │   ├── css/                    # BEM
│   │   │   └── js/                     # Vanilla JS — EventSource, service worker do totem/painel
│   │   └── routes/, public/, storage/, tests/, config/
│   │
│   └── admin/                          # sisrecepcao-admin — deploy Coolify #2
│       ├── app/
│       │   ├── Http/Controllers/
│       │   │   ├── Diretoria/          # Seção 8.1 — painel estatístico + relatório PDF
│       │   │   ├── Bi/                 # Módulo 2.5 — dashboards
│       │   │   ├── Usuarios/           # Seção 8.3 — perfis Developer/Admin/Servidor
│       │   │   └── Configuracoes/      # Seção 8.4 — dados institucionais, identidade visual, versão
│       │   ├── Services/
│       │   │   ├── Bi/                 # Agregação de indicadores, geração de PDF, expurgo/anonimização (LGPD)
│       │   │   └── Deploy/             # Auto-atualização via GitHub + migrações + gatilho Coolify (Seção 8.4)
│       │   ├── Models/                 # Eloquent — Configuracao, Usuario, LogAuditoria (sisrecepcao_db)
│       │   └── Auth/                   # Cliente OIDC do Keycloak (Seção 5.4/8.6)
│       ├── database/migrations/        # Tabelas próprias: configuracoes, perfis, auditoria LGPD, cache de BI
│       ├── resources/
│       │   ├── views/{home,admin,bi,legal,errors}/  # Blade — home institucional, legal (8.7), 404/500 (8.7)
│       │   ├── css/                    # BEM
│       │   └── js/                     # Vanilla JS
│       └── routes/, public/, storage/, tests/, config/
│
├── infra/
│   ├── keycloak/                       # Realm/config do IdP, compartilhado pelos dois apps (Seção 3.1/5.4)
│   ├── mercure/                        # Config do hub Mercure compartilhado com o NovoSGA (Seção 5.2)
│   └── coolify/                        # Dois serviços de deploy — atendimento e admin (Seção 7.5/8.9)
│
└── docs/
    ├── recepcao.md                     # Este documento — especificação viva do projeto
    ├── novosga-2.3 - Gov/              # Referência analisada (Seção 5) — motor de filas
    ├── dbeaver-devel/                   # Referência analisada (Seção 6.1) — ferramenta de dev
    ├── cloudbeaver-devel/               # Referência analisada (Seção 6.2) — admin web do banco
    └── mod-sei-pen-master/              # Avaliado e descartado para este escopo (integração SEI↔SEI, não citizen-facing)
```

### 7.3. Justificativa das Escolhas

| Pasta | Por que existe | Depende de |
| :--- | :--- | :--- |
| `apps/atendimento` | Isola o que **não pode parar durante o expediente** (emissão de senha, painel, notificação de setor) em um deploy próprio, sem o risco de auto-atualização do app administrativo (Seção 7.5). | Seção 2 (especificação funcional) |
| `apps/admin` | Concentra o que é **naturalmente mais arriscado** (auto-deploy via GitHub, migrações de banco, painel de Configurações) longe do caminho crítico do atendimento presencial. | Seção 8 |
| `apps/atendimento/app/Services/Novosga` | Nenhuma view fala diretamente com o NovoSGA — centraliza credenciais OAuth2/Mercure e a lógica de crachá/LGPD que não existe no NovoSGA. | Seção 5.1 |
| `database/migrations` (sisrecepcao_db, compartilhado) | Banco **separado** do `novosga_db` (mesmo servidor MySQL, schemas distintos) — preserva o Modelo 1 (Seção 5.4): o SisRecepção nunca escreve direto nas tabelas do NovoSGA. | Seção 5.3/5.4 |
| `*/app/Auth` + `infra/keycloak` | Implementa a decisão da Seção 5.4/8.6 — Keycloak como IdP central e único para os dois apps; cada app traduz o login Keycloak para a credencial de conta de serviço do NovoSGA quando precisa chamar a API de filas. | Seção 5.4 |
| `apps/atendimento/app/Services/Protocolo` | Ponto de extensão preparado, sem implementação — futura integração com o webservice do SEI. | Seção 7.4, item 1 |
| `docs/mod-sei-pen-master` mantido só como referência | Avaliado e **não integrado por ora** — resolve trâmite SEI↔SEI entre instituições, não o atendimento presencial da recepção. A SMS opera SEI internamente; ver decisão registrada na Seção 7.4, item 1, para o caminho de extensão futura. | — |

### 7.4. Decisões Registradas

1. **SEI (decidido):** a SMS opera SEI internamente. O módulo "Entrega de Documentos/Protocolo" (2.1) **não integra com o SEI agora** — a implementação atual trata o protocolo apenas como categoria de fila do NovoSGA/totem. A pasta `apps/atendimento/app/Services/Protocolo` deve deixar um ponto de extensão preparado (interface clara, sem implementação) para que, no futuro, a criação de um `Atendimento` de "Entrega de Documentos" possa disparar a criação de um processo real no SEI via **webservice próprio do SEI** (não o mod-sei-pen, que resolve trâmite SEI↔SEI entre instituições, não abertura de processo por atendimento presencial). Nenhum código de integração deve ser escrito nesta fase.
2. **Autenticação (decidido):** ver Seção 5.4, item 3 — Keycloak como IdP central + conta de serviço no NovoSGA, com atribuição do atendente preservada via `AtendimentoMeta`.
3. **Validação do NovoSGA em MySQL (esclarecido, não é tarefa da equipe do SisRecepção):** é um passo único de homologação a ser executado por quem for implantar o NovoSGA — apontar `DATABASE_URL` para MySQL/MariaDB local e rodar a suíte PHPUnit já existente no próprio NovoSGA (`phpunit.dist.xml`) antes do go-live, para confirmar ausência de divergência de comportamento entre PostgreSQL (ambiente oficial de desenvolvimento deles) e MySQL (banco escolhido aqui, Seção 5.3). Não bloqueia o início do desenvolvimento do SisRecepção em si, apenas a entrada em produção do NovoSGA sobre MySQL.

### 7.5. Avaliação de Operacionalidade: Por Que Dois Apps em Vez de Um

Registro da avaliação que motivou a divisão acima, feita sob a ótica de operação real (não só de organização de código):

* **Risco identificado:** a Seção 8.4 coloca auto-atualização via GitHub, execução de migrações de banco e gatilho de deploy via Coolify dentro do painel de Configurações. Em um app único, uma atualização mal-sucedida do módulo administrativo derrubaria, junto, a emissão de senha para toda a recepção — incluindo triagem prioritária (idoso, gestante, PCD) — contrariando a exigência de "Operação Resiliente" já registrada na Seção 2.5.
* **O que não é problema:** o painel de TV e o console do atendente já se conectam diretamente ao Mercure do NovoSGA via `EventSource` no navegador (Seção 5.1/2.6), não dependendo de round-trip contínuo ao Laravel — uma reinicialização breve do app de atendimento não apaga o que já está na tela, apenas pausa a emissão de senha nova por alguns segundos.
* **Decisão:** separar em `sisrecepcao-atendimento` (alta disponibilidade, deploy raro, sem auto-atualização embutida) e `sisrecepcao-admin` (onde vive o auto-deploy/migrações), mantendo a mesma stack Laravel (Seção 8.6) e o mesmo banco/IdP compartilhados. Um erro de atualização no app administrativo deixa de poder tirar visitante da fila.

---

## 8. Requisitos Institucionais, Painel Administrativo e Publicação

Requisitos definidos para a camada institucional do SisRecepção (portal, autenticação, painel administrativo, publicação), a serem conciliados com a arquitetura de filas das Seções 3–7 (ver 8.6).

### 8.1. Painel Exclusivo da Diretoria Administrativa

* Painel de acesso restrito ao perfil da **Diretoria Administrativa** (Seção 1.2), exibindo dados estatísticos consolidados: número de atendimentos, tempo médio de atendimento, tempo de espera, volume por setor — reaproveitando os indicadores já especificados no módulo de BI (Seção 2.5).
* **Relatório imprimível em PDF**, gerado a partir dos mesmos dados do painel, para prestação de contas e reuniões da Diretoria.

### 8.2. Acesso a Outros Sistemas da SMS

O sistema deve **linkar o acesso/login** aos demais sistemas já usados pela SMS: **SisPec, SEI, TI Conecta e SisEscala**. *(Pendência a esclarecer: se "linkar" significa apenas atalhos/links de acesso rápido a partir do portal — cada sistema com seu próprio login — ou se é esperado SSO real entre eles e o Keycloak do SisRecepção, Seção 5.4. O segundo caso exige levantar, para cada sistema, se ele suporta OIDC/SAML, antes de assumir viabilidade.)*

### 8.3. Perfis de Usuário

| Perfil | Escopo |
| :--- | :--- |
| **Developer** (Super Usuário) | Acesso total, incluindo o Painel de Configurações e Manutenção (Seção 8.4). |
| **Admin** | Gestão do sistema e dados gerais da SMS. |
| **Servidor** | Acesso operacional ao seu setor/atuação (console do atendente, Seção 2.4). |

### 8.4. Painel de Configurações e Manutenção (exclusivo Developer)

Painel restrito ao perfil Developer, contemplando:

* Dados institucionais da Secretaria (nome, identidade, contatos).
* Identidade visual: favicon, og-images, logo.
* Versão do sistema (exibida no rodapé/painel).
* Atualização do sistema a partir do **GitHub** (pull/deploy).
* Execução de **migrações no banco de dados**.
* Atualizações via **Coolify** (plataforma de publicação — Seção 8.9).

### 8.5. Página Inicial

Layout moderno, intuitivo e clean, nas **cores da gestão atual da prefeitura**. Página institucional pública, com redirecionamento de usuários autenticados para o painel correspondente ao seu perfil.

### 8.6. Stack Tecnológica: PHP 8.3 + Laravel (decidido)

**Decisão registrada:** **PHP 8.3 + Laravel como stack única** de todo o SisRecepção — não só a camada institucional deste capítulo, mas também o back-end de filas (totem, painel de TV, console do atendente, BI) especificado nas Seções 2–5. Isso substitui a proposta original polyglot (Node.js/NestJS ou FastAPI + React/Vue/Next.js) da Seção 3.1, já atualizada, e a árvore de diretórios da Seção 7.2, já revisada para o layout padrão de um app Laravel.

Motivo da escolha: consistência com o padrão já validado no GNIRSaude (mesmo desenvolvedor, mesma stack — PHP 8.3+/Laravel/MySQL/JS vanilla), uma única equipe mantendo um único tipo de aplicação, e nenhuma perda funcional — o Laravel consome a API REST + Mercure (SSE) do NovoSGA da mesma forma já especificada nas Seções 5.1 e 2.6, independentemente da linguagem do lado do SisRecepção.

### 8.7. Páginas de Erro e Legais

* Páginas de erro personalizadas: **404** e **500**.
* Páginas legais: **Termos de Uso**, **Política de Privacidade** e **LGPD** — mesmo padrão já adotado no GNIRSaude (data de atualização dinâmica, dados institucionais vindos do painel de Configurações).

### 8.8. Segurança Web (obrigatório — sistema de órgão público)

Requisitos mínimos de segurança para um sistema público municipal manipulando dados de servidores, visitantes e (indiretamente, via módulos futuros) pacientes:

* **Formulários públicos (contato, cadastro, totem):** proteção anti-spam/bot — honeypot + rate limiting por IP nos endpoints de submissão; CAPTCHA (ex.: Cloudflare Turnstile/hCaptcha) em formulários expostos sem autenticação; validação e sanitização de entrada no servidor (nunca confiar em validação client-side).
* **CSRF:** proteção nativa do Laravel (`@csrf`/`VerifyCsrfToken`) habilitada em todos os formulários autenticados.
* **XSS:** escape automático do Blade (`{{ }}`) como padrão; nunca usar `{!! !!}` com dado não sanitizado; `Content-Security-Policy` configurado.
* **SQL Injection:** uso exclusivo de Eloquent/Query Builder com bindings parametrizados — nunca concatenar SQL cru.
* **Autenticação:** hashing via `bcrypt`/`argon2id` (padrão Laravel); **2FA obrigatório para os perfis Developer e Admin**, dado o nível de acesso (painel de configurações, migrações de banco); política de senha forte e bloqueio por tentativas (throttle de login).
* **Autorização:** policies/gates do Laravel para todo endpoint sensível, validando perfil (Developer/Admin/Servidor) — nunca confiar apenas na ocultação de UI.
* **Sessões e cookies:** `Secure`, `HttpOnly`, `SameSite=Lax/Strict`; sessão expirando por inatividade nos painéis administrativos.
* **HTTPS obrigatório:** HSTS habilitado; forçar redirecionamento HTTP→HTTPS (`https://recpcao.atb.app.br` — Seção 8.9).
* **Uploads (crachá/OCR, logo, favicon, og-images):** validação de tipo real do arquivo (magic bytes, não só extensão), limite de tamanho, armazenamento fora da webroot pública ou com nomes não previsíveis, sem execução de script no diretório de upload.
* **Segredos:** `.env` fora do versionamento (já é convenção no GNIRSaude); segredos de produção geridos via variáveis de ambiente do Coolify, nunca commitados.
* **Dependências:** `composer audit` / Dependabot habilitado no repositório GitHub; atualização de dependências com CVE conhecida tratada como prioridade.
* **Auditoria:** log de ações administrativas sensíveis (alteração de configurações, migrações, atualização via GitHub/Coolify) com usuário, IP e timestamp — relevante inclusive para rastrear quem disparou uma atualização de produção.
* **LGPD:** reforça o já especificado na Seção 2.5 — mascaramento de dados pessoais em telas públicas/relatórios, e página legal de LGPD (Seção 8.7) descrevendo tratamento de dados de visitantes/servidores.

### 8.9. Infraestrutura e Publicação

* **Domínio:** `https://recpcao.atb.app.br` *(confirmar grafia — divergente de "recepção"/"recepcao"; registrar aqui exatamente como informado)*.
* **Hospedagem:** servidor próprio (não cloud gerenciado por terceiros).
* **Plataforma de publicação/deploy:** **Coolify**, autoatualizável a partir do painel de Configurações (Seção 8.4) — pull do GitHub, migrações de banco e deploy.
* **Repositório:** GitHub — `HMOsorios/sisrecepcao` *(confirmar organização/usuário exato antes de criar o repositório).*

---

## 9. Lacunas Operacionais e Soluções Propostas

Auditoria crítica do documento (feita a pedido, antes de confirmar que a especificação está completa) identificou 7 lacunas reais, não cobertas pelas Seções 1–8. Esta seção registra cada uma com a solução proposta, seguindo boas práticas de mercado e normas específicas para sistema público brasileiro. **Status: soluções propostas e aceitas na direção geral — parâmetros específicos (ex.: RPO/RTO exatos, fornecedor de VPN) seguem em aberto para detalhamento futuro.**

### 9.1. Contradição Off-line vs. NovoSGA como Fonte Única de Numeração

**Lacuna:** a Seção 2.5 exige operação off-line/local, mas a Seção 5.4 fixou o NovoSGA como única fonte de verdade da numeração de senhas — se o NovoSGA ou a rede até ele cair, o totem não tem como emitir uma senha numericamente válida.

**Solução — padrão outbox local + reconciliação** (mesmo padrão usado em sistemas de POS/retail para operação offline-first):

* Ao detectar indisponibilidade do NovoSGA (timeout/circuit breaker), o `sisrecepcao-atendimento` emite **senha provisória local** (prefixo distinto, ex.: `OFF-A001`), gravada em tabela de outbox no `sisrecepcao_db` com `dataChegada` real.
* Job assíncrono (Laravel Queue) reenvia essas senhas para `POST /api/distribui` do NovoSGA assim que a conectividade volta, preservando a ordem real de chegada; o ticket impresso já avisa o visitante que é provisório.
* **3 níveis de degradação** explícitos: (1) NovoSGA lento → retry com backoff; (2) NovoSGA indisponível → outbox local; (3) rede totalmente fora → atendimento manual assistido.
* Painel/console cacheiam no navegador (`localStorage`) o último estado recebido via Mercure, evitando tela em branco quando o hub cai junto com o NovoSGA.
* Circuit breaker dispara alerta automático de TI ao entrar em modo de contingência (Seção 9.2/9.4 — monitoramento).

### 9.2. Backup e Continuidade

**Lacuna:** a Seção 8.9 define servidor próprio (sem redundância gerenciada por nuvem), mas o documento não trata backup, RTO/RPO nem plano de continuidade.

**Solução:**

* Regra **3-2-1**: 3 cópias, 2 mídias diferentes, 1 fora do servidor físico (object storage externo tipo Backblaze B2/Cloudflare R2, ou segunda máquina em outro prédio da SMS).
* Dump diário do MySQL (ou hot backup via `mariabackup`/Percona XtraBackup) **+ binlog habilitado** para *point-in-time recovery*, não só snapshot da última noite.
* Pacote `spatie/laravel-backup`: agenda dump do banco + arquivos de upload (fotos de crachá), envia para disco remoto, notifica por e-mail/Slack em caso de falha — agendável pelo scheduler do Coolify.
* Definir **RPO e RTO explícitos** (ex.: RPO ≤ 24h, RTO ≤ 4h) como requisito formal.
* **Testar restauração trimestralmente** — backup nunca restaurado não é backup confiável.
* Resiliência mínima de hardware: RAID em disco, nobreak (UPS), e idealmente um segundo servidor em standby (failover manual aceitável nesta fase).

### 9.3. Segmentação de Rede

**Lacuna:** a Seção 8.8 cobre segurança de aplicação web, mas nada sobre a rede física — um totem de acesso público não deveria estar na mesma rede dos sistemas administrativos internos da SMS.

**Solução:**

* **VLAN dedicada** para os terminais públicos (totem, TV, crachá), com saída liberada só para a porta HTTPS do `sisrecepcao-atendimento`, sem acesso a nenhum outro sistema/segmento interno.
* **Modo kiosk real no SO** (Windows Assigned Access ou Chromium `--kiosk` em Linux travado, sem shell/task switcher acessível).
* Firewall default-deny entre VLANs; o `sisrecepcao-admin` **não deve ser acessível pela rede do totem** — idealmente atrás de VPN ou allowlist de IP para login Developer/Admin.
* Reverse proxy do Coolify (Traefik) com WAF na frente (Cloudflare ou ModSecurity) como camada extra.
* Segurança física: gabinete anti-vandalismo no totem, portas USB desabilitadas.

### 9.4. Deploy Automático sem Gate de CI

**Lacuna:** a Seção 8.4 permite atualizar o `sisrecepcao-admin` direto do GitHub, sem pipeline de testes antes do pull — um commit quebrado vai direto para produção.

**Solução:**

* Pipeline **GitHub Actions** obrigatório antes de merge no branch de produção: testes (Pest/PHPUnit), análise estática (PHPStan/Larastan), padrão de código (Laravel Pint) — branch protegido exigindo esses checks.
* Coolify observa **apenas o branch protegido** (`main`/`production`); push em branch de feature nunca dispara deploy.
* **Ambiente de staging** no Coolify antes de produção: deploy automático em staging, smoke test, promoção manual (ou aprovação obrigatória via GitHub Environments) para produção.
* **Health check pós-deploy** (`/health`) antes do Coolify cortar tráfego para o novo container, com rollback automático se falhar.
* Notificação de todo deploy (quem, quando, commit) — reforça a trilha de auditoria já prevista na Seção 8.8.

### 9.5. Acessibilidade Legal (eMAG/WCAG)

**Lacuna:** a Seção 2.1 menciona áudio e contraste no totem, mas não cita norma de conformidade — para sistema público brasileiro, acessibilidade é exigência normativa, não só boa prática.

**Solução:**

* Adotar formalmente o **eMAG 3.1** (Modelo de Acessibilidade em Governo Eletrônico) como padrão de conformidade — mapeia quase integralmente ao WCAG 2.1 AA.
* HTML semântico + ARIA em todo elemento interativo do totem, navegação 100% por teclado/switch-access, alvo de toque mínimo 44×44px, contraste ≥ 4,5:1, texto redimensionável sem quebrar layout.
* **VLibras** (widget oficial do Governo Federal para tradução em Libras, gratuito, embutível via script) no totem e no portal institucional.
* Teste automatizado de acessibilidade no CI (axe-core/pa11y), integrado ao pipeline da Seção 9.4.
* Altura/alcance físico do totem conforme **NBR 9050** (acessibilidade em equipamentos/espaços físicos).

### 9.6. Governança LGPD Operacional

**Lacuna:** as Seções 2.5/8.8 cobrem mascaramento/criptografia/expurgo tecnicamente, mas faltam os elementos de governança exigidos pela LGPD/ANPD.

**Solução:**

* Publicar o **Encarregado de Dados (DPO)** com nome/contato na página de LGPD (Seção 8.7) — exigência da ANPD.
* Documentar a **base legal** por categoria de dado (ex.: execução de política pública — Art. 7º, III, para dados de atendimento; cumprimento de obrigação legal para dados de servidor/RH).
* Definir **prazo de retenção explícito** por tipo de dado (ex.: foto/documento de crachá expurgado N dias após a visita), parametrizando a rotina de expurgo já prevista na Seção 2.5.
* Fluxo de **direitos do titular** (Art. 18 LGPD — acesso, correção, exclusão) roteado pela **Ouvidoria** (setor já existente, Seção 1.2), com formulário dedicado.
* Avaliar um **RIPD** (Relatório de Impacto à Proteção de Dados) dado o volume de CPF/foto coletado.
* Checar cláusula de transferência internacional de dados nas APIs de terceiros usadas (TTS em nuvem — AWS Polly/Google Cloud TTS; CAPTCHA).

### 9.7. Camada de Hardware

**Lacuna:** o documento é inteiramente de software — a integração física (impressora, totem, webcam, TV) não tem nenhuma linha registrada.

**Solução:**

* **Impressora térmica ESC/POS** (padrão de mercado, ex.: Epson TM-T20/T88 ou equivalentes nacionais Elgin/Bematech) — como navegador não fala ESC/POS diretamente, usar uma **ponte de impressão local** (serviço rodando na máquina do totem, tipo QZ Tray, expondo API `localhost` chamada pelo Blade/JS).
* Totem: mini-PC ou thin client em gabinete kiosk anti-vandalismo, em modo travado (Seção 9.3).
* OCR: webcam para leitura básica; **scanner de documento dedicado** para maior precisão de CPF/RG, com fallback de digitação manual quando o OCR falhar.
* Painel de TV: dispositivo de sinalização dedicado (Raspberry Pi / Android TV box) rodando o painel em navegador kiosk via HDMI, em vez de depender do app nativo da Smart TV — prática padrão de digital signage, mais fácil de monitorar remotamente.
* Nobreak (UPS) no totem, impressora e servidor — mínimo para sustentar a operação resiliente da Seção 2.5 em queda breve de energia.

---

## 10. Confirmação de Completude para Início do Desenvolvimento

Auditoria final do documento (revisão de todas as seções, incluindo checagem de referências cruzadas deixadas por mudanças de decisão anteriores, já corrigidas) para responder à pergunta: **há algo que impeça o início do desenvolvimento do SisRecepção seguindo o que está registrado neste documento?**

**Resposta: não.** Todas as pendências remanescentes se enquadram em uma das três categorias abaixo — nenhuma delas é uma contradição arquitetural ou uma decisão estrutural em aberto.

### 10.1. Pendências de Decisão Futura (não bloqueiam início)

1. **Seção 8.2** — SSO real vs. link simples para SisPec/SEI/TI Conecta/SisEscala. Afeta só essa funcionalidade pontual; o restante do sistema não depende disso.
2. **Seção 9.2** — RPO/RTO exatos do backup. A direção (regra 3-2-1, `spatie/laravel-backup`, binlog) já está definida; os números específicos são parâmetro de configuração.
3. **Seção 9.3** — Segmentação de rede/VLAN. Tarefa de infraestrutura física da TI municipal, ortogonal ao código da aplicação.
4. **Seção 9.6** — Nome do DPO, prazo exato de retenção, RIPD. Insumos jurídicos/administrativos da própria SMS; a rotina de expurgo é construída com o prazo como variável configurável, preenchida depois.
5. **Seção 9.7** — Modelo exato de impressora/totem/hardware. Decisão de compra; o código é feito contra a interface documentada (ESC/POS) e integrado quando o hardware chegar.
6. **Seção 5.4, item 3** — Validação do NovoSGA sob MySQL. Já registrado como bloqueio apenas para a entrada em produção do NovoSGA, não para o início do desenvolvimento do SisRecepção.

### 10.2. Confirmações Administrativas Simples (resolver nos primeiros dias)

* **Seção 8.9** — Grafia exata do domínio (`recpcao.atb.app.br`) e do repositório (`HMOsorios/sisrecepcao`) — confirmar o texto antes de configurar DNS/criar o repositório.

### 10.3. Recomendação de Processo (não é pendência de decisão)

* **Seção 9.4** — O pipeline de CI (testes/lint antes do deploy) deve ser montado junto com o scaffolding inicial do projeto, não depois — parte do "dia 1" de desenvolvimento, mesmo não bloqueando a escrita das primeiras regras de negócio.

### 10.4. Declaração de Confirmação

Não há mais nenhuma contradição arquitetural em aberto — a mais séria identificada (operação off-line vs. NovoSGA como fonte única de numeração, Seção 2.5 vs. Seção 5.4) já tem solução registrada na Seção 9.1. As referências cruzadas que citavam a stack tecnológica anterior (Node/FastAPI/Kubernetes/`services/api-bff`), substituída pela decisão de Laravel em dois apps (Seções 3.1, 7 e 8.6), já foram corrigidas.

**Seguindo o que está registrado no `recepcao.md` até esta seção, não há nada que impeça iniciar o desenvolvimento.** As pendências restantes são parametrizações, confirmações administrativas ou decisões de negócio/compra que se resolvem em paralelo à implementação, sem travar o código.

**Ressalva honesta:** esta confirmação vale para a completude do *planejamento* — como qualquer especificação, é natural que hardware real, usuários reais ou a própria SMS tragam ajustes durante a implementação. Isso não é uma lacuna do documento; é o processo normal de desenvolvimento de software.

