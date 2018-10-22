# Useful commands

## Running tests

```bash
docker-compose up
```

and then

**Unit tests**

```bash
docker exec -it pepiscms_web_1 sh -c "composer install && ./vendor/bin/phpunit -c ./vendor/piotrpolak/pepiscms/"
```

**Smoke tests (behat)**

```bash
docker exec -it pepiscms_web_1 sh -c "composer install && vendor/bin/behat"
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