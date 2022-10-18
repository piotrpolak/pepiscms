# Releasing a new version

- Run the CI build with version matrix
- Run Docker Compose environments for PHP 5.x and PHP 7.x
- Check manual installation by setting `PEPIS_CMS_IS_UNATTENDED_INSTALL` to false in `docker-compose.yml`,
  then navigate to http://localhost/install.php
- Increase release version in `composer.json`
- Describe changes in `CHANGELOG.md`
- Create a GitHub tag and a GitHub release