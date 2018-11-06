# Useful commands

## Running tests

```bash
docker-compose up
```

and then

**Unit tests**

```bash
docker exec -it pepiscms_web_1 sh -c "composer --no-suggest --prefer-dist --prefer-stable require phpoffice/phpspreadsheet 1.5.* twig/twig && \
    ./vendor/bin/phpunit -c ./vendor/piotrpolak/pepiscms/phpunit.xml.dist"
```

**Smoke tests (behat)**

```bash
docker exec -it pepiscms_web_1 sh -c "composer install --no-suggest --prefer-dist && vendor/bin/behat --config vendor/piotrpolak/pepiscms/behat.yml"
```

**Entering bash shell**

```bash
docker exec -it pepiscms_web_1 bash
```

## Docker cleanup

```bash
docker-compose rm --stop
```

## Regenerating autoload

```bash
docker exec -it pepiscms_web_1 composer dump-autoload
```

## Using PepisCMS command line commands

```bash
php index.php tools index
```

## Checking code syntax

```bash
./check-code.sh
```

## Fixing code style

```bash
docker exec -it pepiscms_web_1 bash -c "composer require --dev friendsofphp/php-cs-fixer \"2.2.*\" && ./vendor/bin/php-cs-fixer fix"
```

## Optimizing documentation images

```bash
optipng *.png
```

## Validating build locally (it deletes the local app working directory!)

```bash
sudo rm -rf app/ && docker-compose rm --stop -f && docker-compose up --build && \
docker exec -it pepiscms_web_1 sh -c \
"composer install && composer require --no-update phpoffice/phpspreadsheet 1.5.* && composer --no-update require twig/twig && ./vendor/bin/phpunit -c ./vendor/piotrpolak/pepiscms/phpunit.xml.dist && vendor/bin/behat"
```

## Rebuilding assets

### Installing compass utility

```bash
docker exec -it pepiscms_web_1 bash -c "apt-get update && apt-get install -y ruby-compass"
```

### Running compass watch

```bash
docker exec -it pepiscms_web_1 bash -c "cd pepiscms/theme && compass watch"
```

## Restarting everything

```bash
sudo docker-compose rm --stop -f && sudo rm -rf tmp/html/ && sudo docker-compose up
```