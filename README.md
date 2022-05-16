# Tag Tools

![Elgg 4.2](https://img.shields.io/badge/Elgg-4.2-green.svg)
[![Build Status](https://scrutinizer-ci.com/g/ColdTrick/tag_tools/badges/build.png?b=master)](https://scrutinizer-ci.com/g/ColdTrick/tag_tools/build-status/master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/ColdTrick/tag_tools/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/ColdTrick/tag_tools/?branch=master)
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
