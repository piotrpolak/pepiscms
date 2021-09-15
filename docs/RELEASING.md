# Releasing a new version

- Run the CI build with version matrix
- Run Docker Compose environments for PHP 5.x and PHP 7.x
- Increase release version in `composer.json`
- Describe changes in `CHANGELOG.md`
- Create a GitHub tag and a GitHub release