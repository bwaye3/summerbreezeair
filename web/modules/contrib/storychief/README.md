INTRODUCTION
------------

Use StoryChief for collaboration on SEO blogposts, social posts and one-click 
multichannel distribution. Gain better collaboration, create more interactive 
and beautiful content with StoryChief and go Multi-Channel without any hurdles 
or technical requirements.

[![StoryChief 1 min explanatory video](http://img.youtube.com/vi/BUOGcMwQ6Sc/0.jpg)](https://www.youtube.com/watch?v=BUOGcMwQ6Sc)

[Sign up for free](https://app.storychief.io/register)

*  For a full description of the module, visit the project page:
   https://www.drupal.org/project/storychief

*  To submit bug reports and feature suggestions, or to track changes:
   https://www.drupal.org/project/issues/storychief

REQUIREMENTS
------------

- This plugin requires a StoryChief workspace.
  - Not a StoryChief user yet? [Sign up for free](https://app.storychief.io/register)!


INSTALLATION
------------

- Install as you would normally install a contributed Drupal module. Visit
   https://www.drupal.org/node/1897420 for further information.


CONFIGURATION
-------------

1. Create a Drupal destination in your StoryChief workspace
2. Install and activate the module
3. Configure the module by saving your encryption key & mapping the fields
4. Publish from StoryChief to your Drupal website


DEVELOPERS
----------
StoryChief's Drupal module (versions 8.2 and up) uses Drupal's Annotation-based 
Plugin pattern for mapping StoryChief data to Drupal Fields.

Register additional StoryChiefFieldHandler's by following 
[the PSR4 standard](https://www.drupal.org/docs/8/api/plugin-api/annotations-based-plugins#s-registering-a-plugin).

[More info](https://help.storychief.io/en/articles/4875855-drupal-8-9-mapping-fields)

[Starter kit](https://github.com/Story-Chief/drupal-8-mapping-starter-kit)

**Available hooks:**
- ```hook_storychief_node_type_alter()``` Change the node type to publish at runtime. 

- ```hook_storychief_payload_alter()``` Altering the payload at runtime.

- ```hook_storychief_field_handler_info_alter()``` Remove existing StoryChiefFieldHandler's 
