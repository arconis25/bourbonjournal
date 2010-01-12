
Theme Name: wp_yoghourt
Theme URI: http://web-kreation.com/wp_yoghourt/
Description: A 2 columns layout.
Author: Jeremie Tisseau
Author URI: http://web-kreation.com

	Download: http://web-kreation.com/index.php/freebies/#wordpress

    The CSS, XHTML and design is released under GPL:
	http://www.opensource.org/licenses/gpl-license.php

===========
INSTALLATION
===========

- Unzip the downloaded file.
- Upload the entire "wp_yoghourt" folder to your 'wp-content/themes/" folder
- Login into WordPress administration
- Click on the 'Design" tab
- Click on the "yoghourt" theme thumbnail/screenshot or title

That's it. Go back to the front page of your blog and hit refresh to see your newly installed theme.

======
USAGE
======
#Changing Logo (sidebar.php):
- To change the logo, use a text editor (i.e: Notepad) to open the sidebar.php file (this file is in your theme: /wp_content/themes/yoghourt/sidebar.php)and edit this line to link to your image:
        <img src="wp-content/themes/yoghourt/images/logo.jpg" alt="Home" />

- or name of your logo "logo.jpg" (must be a jpg image) and save it in /wp_content/themes/yoghourt/images/  (you will be prompted to replace logo.jpg).


#Adding items to the main Menu (sidebar.php):
   - The sidebar menu features one link: About
   - To add more links, use a text editor (i.e: Notepad) to open the sidebar.php file (this file is in your theme: /wp_content/themes/yoghourt/sidebar.php) and copy and paste:
	<li><a href="<?php echo get_permalink(2); ?>" title="<?php _e('About'); ?>"><?php _e('About'); ?></a></li>
   - Remember to edit your link title and the destination of your link within href=" ".

 #Editing the header:
   - Open "index.php" /wp_content/themes/yoghourt/index.php) if you want to edit the following lines:
        "Yoghourt v1.0
        Brought to you by Web-Kreation

        This template is released under the Creative Commons Attribution v2.5."


======
LICENSE
======
The CSS, XHTML and design is released under GPL:
	http://www.opensource.org/licenses/gpl-license.php

For any use or distribution of this theme, you must link back to my website and credit me for the original version of it. Please DO NOT REMOVE OR EDIT MY LINK within this theme.



