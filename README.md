<h1 align="center">SuluThemeBundle</h1>

<p align="center">
    <a href="https://sulu.io/" target="_blank">
        <img width="30%" src="https://sulu.io/uploads/media/800x/00/230-Official%20Bundle%20Seal.svg?v=2-6&inline=1" alt="Official Sulu Bundle Badge">
    </a>
</p>

<p align="center">
    <a href="https://github.com/sulu/SuluThemeBundle/blob/master/LICENSE" target="_blank">
        <img src="https://img.shields.io/github/license/sulu/SuluThemeBundle.svg" alt="GitHub license">
    </a>
    <a href="https://github.com/sulu/SuluThemeBundle/releases" target="_blank">
        <img src="https://img.shields.io/github/tag/sulu/SuluThemeBundle.svg" alt="GitHub tag (latest SemVer)">
    </a>
    <a href="https://github.com/sulu/SuluThemeBundle/actions" target="_blank">
        <img src="https://img.shields.io/github/workflow/status/sulu/SuluThemeBundle/Test%20application.svg?label=test-workflow" alt="Test workflow status">
    </a>
    <a href="https://github.com/sulu/sulu/releases" target="_blank">
        <img src="https://img.shields.io/badge/sulu%20compatibility-%3E=2.0-52b6ca.svg" alt="Sulu compatibility">
    </a>
</p>
<br/>

The **SuluThemeBundle** provides the functionality to add multiple themes for different look and feel using multiple 
webspaces in the [Sulu](https://sulu.io/) content management system. 

To achieve this, the bundle uses the [SyliusThemeBundle](https://github.com/Sylius/SyliusThemeBundle) to render different
twig templates and asset files. Each webspace can have it's own theme.

## 🚀&nbsp; Installation and Usage

### Install the bundle

Execute the following [composer](https://getcomposer.org/) command to add the bundle to the dependencies of your 
project:

```bash
composer require sulu/theme-bundle
```

### Enable the bundle 

Enable the bundle by adding it to the list of registered bundles in the `config/bundles.php` file of your project:

```php
return [
    /* ... */
    Sulu\Bundle\ThemeBundle\SuluThemeBundle::class => ['all' => true],
];
```


### Configure the SyliusThemeBundle

In order to use the bundle you have to add at least the following default configuration:

```yaml
# ./config/packages/sylius_theme.yaml

sylius_theme:
    sources:
        filesystem: ~
```

By default, the bundle seeks for the themes in the `%kernel.project_dir%/themes` directory and looks for a
theme configuration file named `composer.json`. This can be changed via the yaml configuration:

```yaml
sylius_theme:
    sources:
        filesystem:
            filename: theme.json
```

For more detailed information about the configuration sources go to the [SyliusThemeBundle documentation](https://github.com/Sylius/SyliusThemeBundle/blob/master/docs/configuration_sources.md).

### Configure your themes

Every theme must have its own configuration file in form of a `theme.json`.
Go to the [Theme Configuration Reference](https://github.com/Sylius/SyliusThemeBundle/blob/master/docs/theme_configuration_reference.md)
for the detailed documentation about the configuration options.

The minimal configuration for a theme would be the following:

```json
// ./themes/<theme-name-1>/theme.json

{
  "name": "vendor/<theme-name-1>"
}
```

It is important, that the `name` matches the naming convention of composer (`vendor/name`). 

### Create a theme
First you have to create the directory `themes` inside your project.
To create a theme you have to create a new directory in the themes folder with the name of the new theme. 
In the newly created directory you have to add the theme configuration file `theme.json`.
See [Configure your themes](#configure-your-themes). Additonally you have to create the `templates` directory next to 
the `theme.json`. Afterwards you have to fill this folder with all the used templates and assets for this theme. 

This results in the following project structure:

```
ProjectName
├── composer.json
├── assets
├── bin
├── config
├── templates
├── themes
│   ├── <theme-name-1>
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

### Add one of your themes to a webspace

Each webspace can use a different theme. A theme can be enabled for a specific webspace by adding the theme name
`<theme>vendor/theme-name</theme>` to your webspace:

```xml
<!-- ./config/webspaces/example.xml -->
<webspace xmlns="http://schemas.sulu.io/webspace/webspace"
          xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
          xsi:schemaLocation="http://schemas.sulu.io/webspace/webspace http://schemas.sulu.io/webspace/webspace-1.1.xsd">
    <name>example.com</name>
    <key>example</key>
    ...
    <theme>vendor/theme-name</theme>
    ...    
</webspace>
```

## ❤️&nbsp; Support and Contributions

The Sulu content management system is a **community-driven open source project** backed by various partner companies. 
We are committed to a fully transparent development process and **highly appreciate any contributions**. 

In case you have questions, we are happy to welcome you in our official [Slack channel](https://sulu.io/services-and-support).
If you found a bug or miss a specific feature, feel free to **file a new issue** with a respective title and description 
on the the [sulu/SuluThemeBundle](https://github.com/sulu/SuluThemeBundle) repository.


## 📘&nbsp; License

The Sulu content management system is released under the under terms of the [MIT License](LICENSE).
