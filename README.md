# MantisBT CSP Report

**MantisCSPReport** - A MantisBT plugin for [Content Security Policy (CSP)](https://developer.mozilla.org/en-US/docs/Web/HTTP/Guides/CSP) reporting.

## Presentation

This plugin allows you to enable the collection of CSP reports from MantisBT pages and view them to identify CSP configuration errors. The plugin inserts the "Content-Security-Policy: report-uri /plugin.php?page=MantisCSPReport/report.php" header and provides an endpoint for collecting reports from client browsers.

![MantisBT CSP Report - CSP Report View Screenshot](https://raw.githubusercontent.com/raspopov/MantisCSPReport/master/MantisCSPReport_view.png)

The plugin also provides some basic statistics:

![MantisBT CSP Report - CSP Graphs Screenshot](https://raw.githubusercontent.com/raspopov/MantisCSPReport/master/MantisCSPReport_graphs.png)

## System Requirements

- MantisBT 2.

## Installation

- Download and extract the plugin files to your computer.
- Copy the MantisCSPReport catalogue into the MantisBT plugin directory.
- In MantisBT, go to the Manage -> Manage Plugins page. You will see a list of installed and currently not installed plugins.
- Click the Install button next to "MantisBT CSP Report" to install a plugin.

## Configuration

- Report retrieval can be temporarily suspended using the "Enable" setting.
- The plugin can be configured to ignore reports from "noisy" sources using the "Ignored sources" setting; by default, "moz-extension, chrome-extension, safari-extension, ms-browser-extension, edge-extension, about".
- The plugin automatically deletes old reports based on the "Max age" setting, which is specified in seconds. Reports are deleted each time a new one is received.

![MantisBT CSP Report - Configuration Screenshot](https://raw.githubusercontent.com/raspopov/MantisCSPReport/master/MantisCSPReport_config.png)

## Access Rights

- To access reports, the user must have the "manage_site_threshold" permissions.
- To manage plugin settings, the user must have the "manage_plugin_threshold" permissions.
