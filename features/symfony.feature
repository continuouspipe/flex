Feature:
  In order to deploy my Symfony applications seamlessly
  As a user
  I want to have the Docker, DockerCompose and ContinuousPipe configuration generated for me

  Scenario: It generates a basic Dockerfile configuration for Symfony
    Given the filesystem looks like the "flex-skeleton" fixtures folder
    When I generate the configuration files
    Then the generated "Dockerfile" file should look like:
    """
    FROM quay.io/continuouspipe/symfony-pack:latest
    ARG APP_ENV=
    ARG APP_DEBUG=
    ARG APP_SECRET=
    COPY . /app/
    WORKDIR /app
    RUN container build
    """

  Scenario: The Docker-Compose file builds the app service
    Given the filesystem looks like the "flex-skeleton" fixtures folder
    When I generate the configuration files
    Then the generated "docker-compose.yml" file should contain at least the following YAML:
    """
    version: '2'
    services:
        app:
            build: .
    """

  Scenario: It adds a database when Symfony has Doctrine enabled
    Given the filesystem looks like the "flex-skeleton" fixtures folder
    And the ".env.dist" file in the filesystem contains:
    """
    ###> symfony/framework-bundle ###
    APP_ENV=dev
    APP_DEBUG=1
    APP_SECRET=547417d8a21a468aa18ba068702c0e9a
    ###< symfony/framework-bundle ###

    ###> doctrine/doctrine-bundle ###
    # Format described at http://docs.doctrine-project.org/projects/doctrine-dbal/en/latest/reference/configuration.html#connecting-using-a-url
    # For a sqlite database, use: "sqlite:///%kernel.project_dir%/var/data.db"
    # Set "serverVersion" to your server version to avoid edge-case exceptions and extra database calls
    DATABASE_URL=mysql://foo:bar@postgres/baz
    ###< doctrine/doctrine-bundle ###
    """
    When I generate the configuration files
    Then the generated "docker-compose.yml" file should contain at least the following YAML:
    """
    version: '2'
    services:
        database:
            image: postgres
    """
