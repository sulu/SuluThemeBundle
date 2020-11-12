# Upgrade

## 2.1

### Switch from LiipThemeBundle to SyliusThemeBundle
To achieve Symfony 5 compatibility the ThemingBundle has to be changed from [LiipThemeBundle](https://github.com/liip/LiipThemeBundle) to [SyliusThemeBundle](https://github.com/Sylius/SyliusThemeBundle).

#### Remove the old theme bundle and install the SyliusThemeBundle:
```bash
# Remove old theme-bundle
composer remove liip/theme-bundle --no-update

# Install new theme-bundle
composer require sylius/theme-bundle
```

#### Configure the SyliusThemeBundle:
In order you to use the bundle you have to add the following default configuration:
```yaml
# ./config/packages/sylius_theme.yaml

sylius_theme:
    sources:
        filesystem: ~
```
By default the bundle seeks for the themes in the `%kernel.project_dir%/themes` directory. This can be changed via the 
yaml configuration:
```yaml
sylius_theme:
    sources:
        filesystem:
            filename: theme.json    #default is composer.json
            directories:
                - "%kernel.project_dir%/templates/themes"
```

#### Theme Configuration
Every theme must have its own configuration file in form of a `composer.json`.
Go to https://github.com/Sylius/SyliusThemeBundle/blob/master/docs/theme_configuration_reference.md for detailed
documentation about the configuration options. The configuration file has to be placed in the specified directory
of the `sylius_theme.yaml`.

The minimal configuration for a theme would be the following:
```json
// ./templates/themes/<theme-name>/theme.json

{
  "name": "vendor/name"
}
````
It is important, that the `name` matches the naming convention of composer.

If your old theme name didn't match the naming convention you also have to change it in the `webspace.xml` to the new one.
```xml
<!-- ./config/webspaces/example.xml -->
<webspace xmlns="http://schemas.sulu.io/webspace/webspace"
          xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
          xsi:schemaLocation="http://schemas.sulu.io/webspace/webspace http://schemas.sulu.io/webspace/webspace-1.1.xsd">
    <name>example.com</name>
    <key>example</key>
    ...
    <theme>vendor/name</theme>
    ...    
</webspace>
```

#### Update project structure
Your themes have to be placed in a `templates` folder next to the `theme.json` file.

For example: `%kernel.project_dir%/templates/themes/<theme-name>/templates`

This results in the following project structure:

```
ProjectName
├── composer.json
├── assets
├── bin
├── config
├── ...
├── templates
│   ├── themes
│   │   ├── <theme-name-1>
│   │   │   ├── templates
│   │   │   │   └── base.html.twig
│   │   │   └── theme.json
│   │   └── <theme-name-2>
│   │       ├── templates
│   │       │   └── base.html.twig
│   │       └── theme.json
|   └── base.html.twig
├── ...
└── ...
```
