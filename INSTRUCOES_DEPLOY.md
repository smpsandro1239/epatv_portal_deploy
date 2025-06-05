# Instruções para Deploy Permanente do Portal de Empregos EPATV

Este documento fornece instruções detalhadas para realizar o deploy permanente do Portal de Empregos da Escola Profissional Amar Terra Verde (EPATV).

## Requisitos do Servidor

Para alojar este portal Laravel em ambiente de produção, o servidor deve atender aos seguintes requisitos:

- PHP 8.1 ou superior
- Extensões PHP: BCMath, Ctype, Fileinfo, JSON, Mbstring, OpenSSL, PDO, Tokenizer, XML
- Composer (gestor de dependências PHP)
- MySQL 5.7+ ou MariaDB 10.3+ (ou SQLite para ambientes mais simples)
- Servidor web (Apache, Nginx)
- SSL/TLS para conexões seguras (recomendado)

## Opções de Alojamento Recomendadas

### 1. Serviços PaaS (Platform as a Service)
- **Laravel Forge + DigitalOcean/Linode/AWS**: Solução gerida especificamente para Laravel
- **Heroku**: Oferece suporte a PHP e Laravel com configuração simples
- **Render**: Plataforma moderna com suporte a PHP e Laravel
- **Platform.sh**: Especializado em PHP com suporte a Laravel

### 2. VPS (Virtual Private Server)
- **DigitalOcean**: Droplets com configuração manual ou one-click apps
- **Linode**: Servidores Linux com bom desempenho
- **AWS EC2**: Instâncias escaláveis
- **Google Cloud Platform**: Compute Engine

### 3. Alojamento Partilhado (Shared Hosting)
- Certifique-se que o provedor suporta Laravel e PHP 8.1+
- Exemplos: SiteGround, A2 Hosting, DreamHost (com suporte a Laravel)

## Passos para Deploy

### Preparação do Ambiente

1. **Configurar o servidor web**:
   - Para Apache, configure um VirtualHost apontando para a pasta `/public`
   - Para Nginx, configure um server block apontando para a pasta `/public`

2. **Configurar o ficheiro .env**:
   - Copie o ficheiro `.env.example` para `.env`
   - Configure as variáveis de ambiente para produção:
     ```
     APP_ENV=production
     APP_DEBUG=false
     APP_URL=https://seu-dominio.com
     
     DB_CONNECTION=mysql
     DB_HOST=seu-host-db
     DB_PORT=3306
     DB_DATABASE=epatv_portal
     DB_USERNAME=seu-usuario
     DB_PASSWORD=sua-senha
     
     MAIL_MAILER=smtp
     MAIL_HOST=seu-servidor-smtp
     MAIL_PORT=587
     MAIL_USERNAME=seu-usuario-email
     MAIL_PASSWORD=sua-senha-email
     MAIL_ENCRYPTION=tls
     MAIL_FROM_ADDRESS=noreply@epatv.pt
     MAIL_FROM_NAME="${APP_NAME}"
     ```

### Deploy do Código

1. **Transferir os ficheiros**:
   - Faça upload de todos os ficheiros para o servidor (via FTP, Git, ou outro método)
   - Alternativa: Clone o repositório diretamente no servidor

2. **Instalar dependências**:
   ```bash
   composer install --optimize-autoloader --no-dev
   ```

3. **Configurar permissões**:
   ```bash
   chmod -R 755 storage bootstrap/cache
   ```

4. **Gerar chave de aplicação**:
   ```bash
   php artisan key:generate
   ```

5. **Executar migrações**:
   ```bash
   php artisan migrate --force
   ```

6. **Semear a base de dados**:
   ```bash
   php artisan db:seed
   ```

7. **Otimizar a aplicação**:
   ```bash
   php artisan optimize
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   ```

### Configuração de Segurança

1. **Configurar HTTPS**:
   - Instale um certificado SSL (Let's Encrypt é gratuito)
   - Configure o servidor web para redirecionar HTTP para HTTPS

2. **Configurar firewall**:
   - Permita apenas tráfego nas portas necessárias (80, 443, SSH)

3. **Configurar backups**:
   - Configure backups automáticos da base de dados e ficheiros

## Manutenção

### Atualizações

Para atualizar o portal após alterações:

1. **Colocar o site em modo de manutenção**:
   ```bash
   php artisan down
   ```

2. **Atualizar o código**:
   - Faça pull das alterações ou upload dos novos ficheiros

3. **Atualizar dependências**:
   ```bash
   composer install --optimize-autoloader --no-dev
   ```

4. **Executar migrações**:
   ```bash
   php artisan migrate --force
   ```

5. **Limpar e reotimizar cache**:
   ```bash
   php artisan optimize:clear
   php artisan optimize
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   ```

6. **Reativar o site**:
   ```bash
   php artisan up
   ```

### Monitorização

- Configure monitorização de uptime (Uptime Robot, Pingdom)
- Configure alertas para erros e problemas de desempenho
- Revise regularmente os logs em `storage/logs`

## Suporte

Para questões técnicas ou suporte adicional, consulte a documentação completa do projeto ou entre em contacto com a equipa de desenvolvimento.

---

Este guia foi preparado para facilitar o deploy permanente do Portal de Empregos EPATV. Adapte as instruções conforme necessário para o seu ambiente específico de alojamento.
