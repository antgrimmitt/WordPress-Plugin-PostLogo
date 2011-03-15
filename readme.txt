=== Post Logo ===
Contributors: antgrimmitt
Tags: post logo, logo, image, get image, same image in post
Requires at least: 2.0.2
Tested up to: 3.1
Stable tag: 1.1b

Post Logo allows you to add a single image to a post using the media browser and you can use the same image in multiple
posts

== Description ==

Post Logo allows you to add a single image to a post using the media browser and you can use the same image in multiple
posts

Features include:

*   Add a single image to a post
*   use the same image in multiple posts
*   Overwrite post image if one already exists

== Installation ==

This section describes how to install the plugin and get it working. Feedback is appreciated still a
beta at the moment 

e.g.

1. Upload `plugin-name.php` to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. When adding a post selected an image to use a post logo and hit save
4. Place `<?php the_post_logo(); ?>` in your templates
5. specify the width height when calling `<?php the_post_logo(array(200,200());?>`
   so that sets the size to 200x200.

== Frequently Asked Questions ==

= Can I have more than one image? =

At the moment no that would be a future development if the its required

== Screenshots ==

1. This shows screenshot screenshot-1.png shows the dialog for adding a logo

== Changelog ==

= 1.1b =

Made a change to how we connect an image to  a post we were searching on filename which cause when using
specifics sizes i.e thumbnail, medium, large etc  we now  strip the image post id out of the class attribute
with the img tag returned by the media browser. Added the ability to specify the width height when calling
the_post_logo() you can do the_post_logo(array(200,200(); so that sets the size to 200x200.
