# Upgrade

## 3.0

### Switch from LiipThemeBundle to SyliusThemeBundle

To achieve Symfony 5 compatibility the ThemingBundle has to be changed from [LiipThemeBundle](https://github.com/liip/LiipThemeBundle) to [SyliusThemeBundle](https://github.com/Sylius/SyliusThemeBundle).

#### Remove the old theme bundle and install the SyliusThemeBundle:

```bash
# Remove old theme-bundle
composer remove liip/theme-bundle --no-update

# Install new theme-bundle
composer require sylius/theme-bundle:"^2.0" --no-update
composer require sulu/theme-bundle:"^3.0"
```

#### Remove old configuration

The old `liip_theme.yaml` configuration needs to be removed:

```diff
- liip_theme:
-     themes: ['project', 'awesome']
-     active_theme: 'awesome'
```

In the next step you see how you configure the **awesome** theme using the SyliusThemeBundle.

#### Configure the SyliusThemeBundle:

In order to use the bundle you have to add the following default configuration:

```yaml
# ./config/packages/sylius_theme.yaml

sylius_theme:
    sources:
        filesystem: ~
```

By default, the bundle seeks for the themes in the `%kernel.project_dir%/themes` directory and looks for a configuration
file named `composer.json`. This can be changed via the yaml configuration:

```yaml
sylius_theme:
    sources:
        filesystem:
            filename: theme.json
```

#### Convert Theme Configuration

In the SyliusThemeBundle every theme must have its own configuration file in form of a `composer.json`.
Add a `theme.json` file and add the following minimal configuration:

```diff
+ {
+     "name": "app/awesome"
+ }
```

Go to the [Theme Configuration Reference](https://github.com/Sylius/SyliusThemeBundle/blob/master/docs/theme_configuration_reference.md)
for the detailed documentation about the configuration options.

Most likely you have to change the theme name. It is important, that the `name` matches the naming convention of composer (`vendor/name`).
Furthermore the `theme.json` has to be moved into the directory for this specific theme. 

For example: `%kernel.project_dir%/themes/awesome/theme.json`

#### Update project structure

Your templates have to be placed in a `templates` directory next to the `theme.json` file.

For example: `%kernel.project_dir%/themes/<theme-name>/templates`

This results in the following project structure:

```
ProjectName
├── composer.json
├── assets
├── bin
├── config
├── templates
├── themes
│   ├── awesome
│   │   ├── templates
│   │   │   └── base.html.twig
│   │   └── theme.json
│   └── <theme-name-2>
│       ├── templates
│       │   └── base.html.twig
│       └── theme.json
├── ...
└── ...
```

As you can see in the project structure, each theme must have their own `theme.json` configuration file next to the
templates directory.

#### Update webspace configuration

If your old theme name didn't match the naming convention you also have to change it in the `webspace.xml` to the new one.

```diff
- <theme>awesome</theme>
+ <theme>app/awesome</theme>
```
