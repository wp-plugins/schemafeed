<div class="wrap">

    <?php screen_icon( 'schemafeed' ); ?>
    
    <h2><?php echo esc_html( __( 'SchemaFeed: Help', 'wpsf' ) ); ?></h2>

    <p>
        SchemaFeed is a plugin that adds the <a href="http://schema.org" target="_blank">schema.org</a> microdata to your web pages.
        Here are a few options for what you can do next. 
    </p>
        
    <ul style="list-style-type: circle; padding-left: 14px;">
        
        <li>Do nothing! Some properties are added automatically to your blog pages.
        
        <li>Enter new schema data. You can do this when you post a standard Post entry.
        
    </ul>
    
    <p>
        There are several benefits to using the schema.org format.
    </p>
    
    <ul style="list-style-type: circle; padding-left: 14px;">
        
        <li>The 3 major search engines will take into account the use of the schema.org schemas in your site to improve on search results. 
        
        <li>You can have a data standard that you can use to provide a data feed to third party users, much like RSS.
        
        <li>schema.org is by no means perfect and will no doubt grow and improve, but starting the
        adoption early and using it is an important element to the success in promoting your website. 
    </ul>
    
    <p>
        There are several routes which the plugin plays out within your WordPress website.
    </p>
    
    <ul style="list-style-type: circle; padding-left: 14px;">
        
        <li>Once the plugin is installed. The basic blog Schema properties will be added to your blog pages automatically. 
        e.g. name, articleBody, wordCount. As some of these properties are meta data for the benefit of search engines, you will
        only see them via your browser View Source.
        
        <li>To see how the other schema types are used, lets take an example. You're writing an article about an ice cream shop you've just
        visited. You start your article via the standard WordPress <b>Add New</b> posting page. Within that page, you will see a 
        new schema.org box where you can add the extra schema properties relating to your posting. You select the "Ice Cream Shop" schema, a 
        list of properties is then shown where you can enter details relevant to the Ice Cream Shop. These properties will then be shown at 
        the bottom of your posting with the relevant search engine friendly tags embedded within it.
        
        <li>Each article can have its own schema, the properties that you add will be displayed automatically at the bottom of each post within the "loop" pages and "single"
        pages. You can change how they are displayed in 
        the <a href="admin.php?page=wp__schema_settings">Schema Settings</a> on the left hand SchemaFeed navigation box.
        
    </ul>
    
    <p>
        The following shows how SchemaFeed generates the schema.org properties:<br />
        <img src="<?php echo wpsf_domain_local_path(); ?>/img/what_is_schema_org.jpg">
    </p>
    
    <p>
        If you need any assistance with this plugin, please contact us on schemafeed.com.
    </p>
    
    <p>
        This plugin is GPLv2 and so is free to use for any personal or commercial website. However, if you plan to use this continuously, we welcome
        any donation to help with the continued development and support of this plugin. We feel that people who donate should also get something 
        in return, as a result, anyone who donates any amount will also get priority email response and support as a result. As a suggestion, 
        we feel $20/year is great value! If you wish to donate, just click on the paypal button to continue.   
    </p>
    
    <form action="https://www.paypal.com/cgi-bin/webscr" method="post">
    <input type="hidden" name="cmd" value="_xclick">
    <input type="hidden" name="business" value="kaichan1@gmail.com">
    <input type="hidden" name="lc" value="US">
    <input type="hidden" name="item_name" value="SchemaFeed WordPress Plugin Support">
    <input type="hidden" name="amount" value="20.00">
    <input type="hidden" name="currency_code" value="USD">
    <input type="hidden" name="button_subtype" value="services">
    <input type="hidden" name="no_note" value="0">
    <input type="hidden" name="bn" value="PP-BuyNowBF:btn_buynowCC_LG.gif:NonHostedGuest">
    <input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_buynowCC_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
    <img alt="" border="0" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" width="1" height="1">
    </form>

    <p>
        Once you have completed your payment, please send your questions and support emails to support@schemafeed.com or just head over to the contact form on schemafeed.com.
    </p>   
    
    <h2><?php echo esc_html( __( 'Documentation', 'wpsf' ) ); ?></h2>
    
    <p>
        The following notes relates to the overall behaviour of the plugin when you add schema properties to a post.
    </p>
    
    <ul style="list-style-type: circle; padding-left: 14px;">
        
        <li>All the schema properties which are not automatically generated are added to the bottom of a posting. Remember some of these
        are meta tags and will not show but is still rendered for search engine purposes. 
        <li>When you add a schema to a posting, each posting will contain the itemtype and itemscope attributes relating to the schema.
        <li>MP3 audio anchor links will be have itemprop="audio" attribute name-value added to it.
        <li>Video anchor links ( format: wmv/avi/mov/mpg/mpeg ) will be have itemprop="video" attribute name-value added to it.
        <li>On single pages, the posting will contain the itemtype and itemscope attributes relating to the schema.
        
    </ul>
    
    <p>
        The following schema properties are populated automatically when the plugin is installed. Some properties are applied across all schemas.              
    </p>     
        
    <table class="widefat" style="width: 60%;">
	
        <thead>
            <tr>
                <th>Schema</th>				
                <th>Property</th>
                <th>Notes</th>
            </tr>
        </thead>
        
        <tbody id="manage_polls">
        
            <tr>
                <td>Blog</td>
                <td>comment</td>
                <td>Comments below the posting.</td>
            </tr>

            <tr>
                <td>UserComments</td>
                <td>commentText</td>
                <td>Individual comments.</td>
            </tr>
            
            <tr>
                <td>WebPage</td>
                <td>&nbsp;</td>
                <td>The Web page body tag will contain the itemscope and itemtype schema attributes.</td>
            </tr>
           
            <tr>
                <td>&nbsp;</td>
                <td>thumbnailUrl</td>
                <td>An img tag is placed at the beginning of the posting within the loop and aligned to the right.</td>
            </tr>
            
            <tr>
                <td>&nbsp;</td>
                <td>keywords</td>
                <td>
                    The tags you enter within a post is rendered as a meta tag and so is not displayed within the browser.
                    <br />
                    e.g. &lt;meta itemprop=&quot;keywords&quot; content=&quot;tag a, tag b&quot;&gt;
                </td>
            </tr>
            
            <tr>
                <td>&nbsp;</td>
                <td>dateCreated</td>
                <td>
                    This is rendered as a meta tag. The first version of the posting is taken as the created date.
                </td>
            </tr>
            
            <tr>
                <td>&nbsp;</td>
                <td>dateModified</td>
                <td>This is rendered as a meta tag. This is the same as the modified date of the posting within WordPress.</td>
            </tr>
            
            <tr>
                <td>&nbsp;</td>
                <td>datePublished</td>
                <td>This is rendered as a meta tag. This is the same as the published date of the posting within WordPress.</td>
            </tr>
            
            <tr>
                <td>&nbsp;</td>
                <td>url</td>
                <td>This is rendered as a meta tag. We take the permalink as the url for this schema property.</td>
            </tr>
            
            <tr>
                <td>&nbsp;</td>
                <td>url</td>
                <td>This is rendered as a meta tag. We take the permalink as the url for this schema property.</td>
            </tr>
            
            <tr>
                <td>&nbsp;</td>
                <td>alternativeHeadline</td>
                <td>This is added to a single page posting before the body content as a H2 tag.</td>
            </tr>
            
            <tr>
                <td>&nbsp;</td>
                <td>description</td>
                <td>
                    This is inserted into the body content and after the alternativeHeadline property.
                    It is rendered within a <strong>bold</strong> tag.
                </td>
            </tr>
            
            <tr>
                <td>&nbsp;</td>
                <td>image</td>
                <td>
                    Each schema can have an image relating to it which corresponds to the itemprop="image" attribute. We will use the first 
                    image within the posting as the main image relating to the schema.
                </td>
            </tr>
            
            <tr>
                <td>&nbsp;</td>
                <td>name</td>
                <td>
                    The name property is added to title in the loop and within a single page.
                </td>
            </tr>
            
            <tr>
                <td>Article</td>
                <td>articleBody</td>
                <td>
                    This is the body content, it applies to the Article schema and the schemas below it, e.g. BlogPosting.
                </td>
            </tr>
            
            <tr>
                <td>Article</td>
                <td>wordCount</td>
                <td>
                    This is the number of words in the article. 
                </td>
            </tr>
            
                       
        </tbody>
        
    </table>     
    
</div>

