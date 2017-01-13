<?xml version="1.0" ?>
<xsl:stylesheet version="1.1" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
<xsl:import href="banshee/main.xslt" />

<!--
//
//  Overview template
//
//-->
<xsl:template match="overview">
<table class="table table-striped table-condensed">
<thead>
<tr><th>Name</th><th>Description</th></tr>
</thead>
<tbody>
<xsl:for-each select="device">
<tr>
<td><a href="/{/output/page}/{@id}"><xsl:value-of select="name" /></a></td>
<td><xsl:value-of select="description" /></td>
</tr>
</xsl:for-each>
</tbody>
</table>

<div class="btn-group">
<a href="/overview" class="btn btn-default">Back</a>
</div>
</xsl:template>

<!--
//
//  Hardware template
//
//-->
<xsl:template match="hardware">
<xsl:if test="description!=''"><div class="panel panel-default panel-body"><xsl:value-of select="description" /></div></xsl:if>

<h2>Applications</h2>
<table class="table table-striped table-condensed">
<thead>
<tr><th>Name</th></tr>
</thead>
<tbody>
<xsl:for-each select="applications/application">
<tr><td><a href="/application/{@id}"><xsl:value-of select="name" /></a></td></tr>
</xsl:for-each>
</tbody>
</table>

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
<img src="/images/icons/hardware.png" class="title_icon" />
<h1><xsl:value-of select="/output/layout_site/title/@page" /></h1>
<xsl:apply-templates select="overview" />
<xsl:apply-templates select="hardware" />
<xsl:apply-templates select="result" />
</xsl:template>

</xsl:stylesheet>
