FROM bitnami/symfony:latest
WORKDIR /app
COPY . /app

RUN apt-get update --fix-missing

RUN apt-get install -y  \
    # npm \
    composer \
    curl

# RUN curl -o- https://raw.githubusercontent.com/nvm-sh/nvm/v0.38.0/install.sh | bash
# RUN echo "export NVM_DIR='$([ -z '${XDG_CONFIG_HOME-}' ] && printf %s '${HOME}/.nvm' || printf %s '${XDG_CONFIG_HOME}/nvm')' [ -s '$NVM_DIR/nvm.sh' ] && \. '$NVM_DIR/nvm.sh'" >> ~/.bash_profile
# RUN source ~/.bashrc
# RUN echo $PATH

# RUN nvm install --lts
# RUN npm install --global yarn

RUN curl -1sLf 'https://dl.cloudsmith.io/public/symfony/stable/setup.deb.sh' | sudo -E bash
RUN apt install symfony-cli

# RUN apt update --fix-missing

RUN composer install
# RUN yarn install
RUN symfony console c:c
RUN symfony console d:d:c
RUN symfony console d:m:m
RUN symfony console d:f:l

CMD ["symfony", "server:start"]
