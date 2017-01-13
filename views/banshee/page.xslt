<?xml version="1.0" ?>
<xsl:stylesheet version="1.1" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
<xsl:import href="main.xslt" />

<!--
//
//  Content template
//
//-->
<xsl:template match="page">
<h1><xsl:value-of select="title" /></h1>
<xsl:value-of disable-output-escaping="yes" select="content" />
<xsl:if test="back">
<div class="btn-group">
<a href="/{back}" class="btn btn-default">Back</a>
</div>
</xsl:if>
</xsl:template>

<!--
//
//  Content template
//
//-->
<xsl:template match="content">
<xsl:apply-templates select="page" />
<xsl:apply-templates select="website_error" />
</xsl:template>

</xsl:stylesheet>
