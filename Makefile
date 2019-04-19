# Makefile

all: build

GIT_COMMIT_ID := $(shell git rev-parse --short HEAD)

include .env
include Makefile.docker
include Makefile.mysql

up: docker_up

down: docker_down

logs: docker_logs

build: docker_build
	cd portal; make build

test:
	cd portal; make test_with_coverage

clean:
	cd portal; make clean

install: mysql_create_database
	cd portal; make install

purge: mysql_drop_database
