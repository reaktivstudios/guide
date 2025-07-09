![Guide Plugin](./rkv-core.jpg "The Guide, from Reaktiv")

This plugin adds docs to the Guide in the WordPress dashboard.

Docs are sourced from either MD or PHP files in the `wp-content/docs` directory. The individual docs should be sorted into folders. The folder name is used as a slug to identify the doc.

Images in MD files can be stored alongside the doc file and referenced with markdown like `![Alt Text](path/image.ext)`

There is an example for the docs directory in the `examples` directory of this plugin.

The path to the docs directory may be altered using these two filters:
- `rkv_guide_docs_path`: Alters the path
- `rkv_guide_docs_url`: Alters the URL

Both filters must be used together to ensure the docs are found, and any embedded URLs are correctly handled.
