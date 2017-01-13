<?xml version="1.0" ?>
<xsl:stylesheet version="1.1" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
<xsl:import href="banshee/main.xslt" />

<!--
//
//  Overview template
//
//-->
<xsl:template match="overview">
overview
</xsl:template>

<!--
//
//  Edit template
//
//-->
<xsl:template match="edit">
<xsl:call-template name="show_messages" />
edit
</xsl:template>

<!--
//
//  Content template
//
//-->
<xsl:template match="content">
<h1>Page title</h1>
<xsl:apply-templates select="overview" />
<xsl:apply-templates select="edit" />
<xsl:apply-templates select="result" />
</xsl:template>

</xsl:stylesheet>
