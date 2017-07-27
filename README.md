# ContinuousPipe Flex

Generates the Docker, DockerCompose and ContinuousPipe configuration automatically for you.

## Applications

The aim is to support various applications, the current support is limited to these type of application:

- Symfony 3.4/4.0

## How to use it?

This can be used locally on your own project and can be activated in ContinuousPipe to create the configuration on the fly.

### Command line

```
composer req continuous-pipe/flex
```

```
bin/cp generate:configuration
```

### In ContinuousPipe

Go in "Features" and activate "Flex".
