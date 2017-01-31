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
//  Used-by template
//
//-->
<xsl:template match="usedby">
<dl>
	<dt>Application:</dt>
	<dd><a href="/application/{application_id}"><xsl:value-of select="application" /></a></dd>
	<dt>Used by:</dt>
	<dd><a href="/business/{business_id}"><xsl:value-of select="business" /></a></dd>
	<dt>Information input:</dt>
	<dd><xsl:value-of select="input" /></dd>
	<dt>Description:</dt>
	<dd class="description"><xsl:value-of select="description" /></dd>
</dl>

<div class="btn-group">
<a href="/overview" class="btn btn-default">Back</a>
</div>
</xsl:template>

<!--
//
//  Content template
//
//-->
<xsl:template match="content">
<h1>Connection</h1>
<xsl:apply-templates select="overview" />
<xsl:apply-templates select="usedby" />
<xsl:apply-templates select="result" />
</xsl:template>

</xsl:stylesheet>
