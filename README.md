# Tag Tools

![Elgg 6.0](https://img.shields.io/badge/Elgg-6.0-green.svg)
![Lint Checks](https://github.com/ColdTrick/tag_tools/actions/workflows/lint.yml/badge.svg?event=push)
[![Latest Stable Version](https://poser.pugx.org/coldtrick/tag_tools/v/stable.svg)](https://packagist.org/packages/coldtrick/tag_tools)
[![License](https://poser.pugx.org/coldtrick/tag_tools/license.svg)](https://packagist.org/packages/coldtrick/tag_tools)

Offers enhancements/tools for tags

## Features

- adds option to follow tags
 - content created with the tag sends notification to follower
 - tag follow configuration can be found at the notifications settings page
- adds an improved version of the tagcloud widget

## Notifications

The tag notifications can be send out using one of the following methods:

- (default) as a separate notification with only information about the tag.
- as an extension on the existing create notification. The tag followers will be added to the subscribers and the content
  of the notification will be appended with some text to indicate the recipient received this notification because of the 
  tags he/she followed.

Which method is used can be configured in the plugin settings.
