FROM node:18

WORKDIR /app

# Copia apenas os arquivos de dependência primeiro (para melhor cache)
COPY package*.json ./

# Limpa o cache do npm antes da instalação
RUN npm cache clean --force && npm install

# Copia o restante do projeto
COPY . .

# Expõe a porta usada pelo Angular
EXPOSE 4200

# Comando de inicialização
CMD ["npm", "start"]
