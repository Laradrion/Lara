<h1>Index Controller</h1>
<p>Welcome to Lara index controller.</p>

<h2>Download</h2>
<p>You can download Lara from github.</p>
<blockquote>
    <code>git clone https://github.com/Laradrion/Lara.git</code>
</blockquote>
<p>You can review its source directly on github: <a href='https://github.com/Laradrion/Lara'>https://github.com/Laradrion/Lara</a></p>

<h2>Installation</h2>
<p>First you have to make the data-directory writable. This is the place where Lara needs
    to be able to write and create files.</p>
<blockquote>
    <code>cd Lara; chmod 777 site/data</code>
</blockquote>

<p>Second, Lara has some modules that need to be initialised. You can do this through a 
    controller. Point your browser to the following link.</p>
<blockquote>
    <a href='<?= create_url('modules/install') ?>'>modules/install</a>
</blockquote>

<p>Third, Lara assumes the installation is made in the root directory of your website.
If this is not the case you will need to change the rewrite base in .htaccess at line 219 to match your site.
Line 218 shows you an example.</p>

<p>Fourth, Lara includes a demo. In site/config.php the main configuration resides.
Replace the file with site/democonfig.php to see a demo site.
When you are done you may wish to remove the example configurations:</p>
<blockquote>
    <code>cd Lara; cd site <br>
        rm democonfig.php; rm installconfig.php</code>
</blockquote>

<p>Fifth, demo controllers reside in site/src. Unused controllers may be removed. If the demo controllers
are removed, make sure they are removed from the site/config.php aswell.</p>
