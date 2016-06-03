FROM tutum/lamp:latest
ARG MYSQL_HOST=127.0.0.1
ARG MYSQL_PORT=3306
ARG MYSQL_USER=root
ARG MYSQL_PASS=
ARG MYSQL_DB_NAME=traceryhosting
RUN rm -fr /app && git clone https://github.com/pwoolcoc/traceryhosting-frontend.git /app
RUN /app/db/run_migrations.sh
EXPOSE 80
CMD ["/run.sh"]
