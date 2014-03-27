Veuse-Portfolio
===============

Veuse Portfolio by Veuse is an easy to use tool for showcasing your projects on your WordPress site.  Display your projects  via a shortcode or widget or template.

<b>This plugin is meant for creating and displaying projects/work on your WordPress website.</b>


**Easy to use**
Publish and categorise your portfolio of projects using a familiar WordPress interface.

**Detailed project information**
Include full project details such as cover images, galleries, categories, client details and projects URLs.

**Fully integrated with WordPress**
Display your portfolio of projects using native WordPress architecture (project archives & single pages). Portfolio can be filtered or paginated, and thumbnail can be enlarged in a lightbox ( optional ).

**Widget or Shortcode**
Portfolios can be inserted on any page or post via shortcode ( shortcode generator ) or with a widget ( If you use a pagebuilder, like <a href="http://wordpress.org/plugins/siteorigin-panels/">Page Builder by Siteorigin</a> )

**Mobile friendly**
Projects includes a responsive layout to integrate with any theme.

**Customisable**
The template files can be overridden in your theme, via hooks and template overrides. The portfolio has very little styling, so you can easily adapt it to suit your website with a little css. 

**Documentation**
Extensive documentation is included in an admin-page. Right where you need it.

**Quick Facts**

- Tags: portfolio, work, projects, gallery, slider
- Requires: 3.7 or higher
- Tested up to: 3.8.1
- License: GPLv2
- License URI: <h href="http://www.gnu.org/licenses/gpl-2.0.html">http://www.gnu.org/licenses/gpl-2.0.html</a>

###Demo

http://veuse.com/demo-veuse-portfolio/


##Customizing the portfolio and projects

Do not change any plugin files directly as they may be overwritten in a future plugin update. Instead, copy the plugin file you want to customize to your theme folder, and change it from there.


###The loop
**File:** /veuse-portfolio/views/front/loop-portfolio.php

The loop is the file that displays the portfolio grid list. The file is commented with some instructions.


###The single project
**File:** /veuse-portfolio/views/front/single-portfolio.php

This is the file that displays the single portfolio-post. The file has markup that makes it work perfectly with the twenty-twelve theme. You may need to change the markup to fit your theme. This file is commented with some instructions.

I have built in hooks for changing the markup, so you don't need to edit the file directly.

###Project meta
**File:** /veuse-portfolio/views/front/project-meta.php

This file contains the projects meta, like project website, launch date, credits and client name.

It also displays categories and skills.

###Project image
**File:** /veuse-portfolio/views/front/project-image.php

This file contains the posts featured image.


##For donations, please follow the link below

<a href="https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=andreas%2ewilthil%40gmail%2ecom&lc=NO&item_name=Veuse&no_note=0&cn=Add%20special%20instructions%20to%20the%20seller%3a&no_shipping=1&currency_code=NOK&bn=PP%2dDonationsBF%3abtn_donateCC_LG%2egif%3aNonHosted"> Donate link</a>
