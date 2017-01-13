<?xml version="1.0" ?>
<xsl:stylesheet version="1.1" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
<xsl:import href="banshee/main.xslt" />

<!--
//
//  Content template
//
//-->
<xsl:template match="content">
<img src="/images/icons/export.png" class="title_icon" />
<h1>Export landscape</h1>
<p>The application landscape can be exported in an ArchiMate format. With Archi, a free ArchiMate modelling tool, you can create a graphical overview of your application landscape. You can use the images you create with Archi in presentations and documentation.</p>
<form action="/{/output/page}" method="post">
<div class="btn-group">
<input type="submit" name="submit_button" value="Export application landscape" class="btn btn-default" />
<a href="http://www.archimatetool.org/" class="btn btn-default">Visit the Archi website</a>
</div>
</form>
</xsl:template>

</xsl:stylesheet>
