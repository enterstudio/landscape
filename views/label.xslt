<?xml version="1.0" ?>
<xsl:stylesheet version="1.1" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
<xsl:import href="banshee/main.xslt" />

<!--
//
//  Overview template
//
//-->
<xsl:template match="overview">
<div class="categories">
<xsl:for-each select="category">
<div class="category">
<h2><xsl:value-of select="@name" /></h2>
<div class="list-group">
<xsl:for-each select="label">
<a href="/{/output/page}/{@id}" class="list-group-item"><xsl:value-of select="." /><span class="badge"><xsl:value-of select="@count" /></span></a>
</xsl:for-each>
</div>
</div>
</xsl:for-each>
</div>

<div id="help">
<p>The number behind a label indicates the number of objects (application or business entity) with that label.</p>
</div>
</xsl:template>

<!--
//
//  Label template
//
//-->
<xsl:template match="label">
<div class="row">
<div class="col-sm-6">

<h2>Applications</h2>
<table class="table table-striped label-condensed">
<thead>
<tr><th>Name</th></tr>
</thead>
<tbody>
<xsl:for-each select="applications/application">
<tr><td><a href="/application/{@id}"><xsl:value-of select="name" /></a></td></tr>
</xsl:for-each>
</tbody>
</table>

</div>
<div class="col-sm-6">

<h2>Business</h2>
<table class="table table-striped label-condensed">
<thead>
<tr><th>Name</th></tr>
</thead>
<tbody>
<xsl:for-each select="business/entity">
<tr><td><a href="/business/{@id}"><xsl:value-of select="name" /></a></td></tr>
</xsl:for-each>
</tbody>
</table>

</div>
</div>

<div class="btn-group">
<a href="/{/output/page}" class="btn btn-default">Back</a>
</div>
</xsl:template>

<!--
//
//  Content template
//
//-->
<xsl:template match="content">
<img src="/images/icons/labels.png" class="title_icon" />
<h1><xsl:value-of select="/output/layout_site/title/@page" /></h1>
<xsl:apply-templates select="overview" />
<xsl:apply-templates select="label" />
<xsl:apply-templates select="result" />
</xsl:template>

</xsl:stylesheet>
