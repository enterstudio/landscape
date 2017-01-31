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
//  Connection template
//
//-->
<xsl:template match="connection">
<dl>
	<dt>From:</dt>
	<dd><a href="/application/{from_application_id}"><xsl:value-of select="from_name" /></a></dd>
	<dt>To:</dt>
	<dd><a href="/application/{to_application_id}"><xsl:value-of select="to_name" /></a></dd>
	<dt>Protocol:</dt>
	<dd><xsl:value-of select="protocol" /></dd>
	<dt>Format:</dt>
	<dd><xsl:value-of select="format" /></dd>
	<dt>Frequency:</dt>
	<dd><xsl:value-of select="frequency" /></dd>
	<dt>Data flow:</dt>
	<dd><xsl:value-of select="data_flow" /></dd>
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
<xsl:apply-templates select="connection" />
<xsl:apply-templates select="result" />
</xsl:template>

</xsl:stylesheet>
