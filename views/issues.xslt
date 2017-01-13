<?xml version="1.0" ?>
<xsl:stylesheet version="1.1" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
<xsl:import href="banshee/main.xslt" />

<!--
//
//  Overview template
//
//-->
<xsl:template match="overview">
<div class="row">

<div class="col-sm-6">
<h2>Applications without owner</h2>
<table class="table table-striped table-condensed no_owner">
<thead>
<tr><th>Name</th></tr>
</thead>
<tbody>
<xsl:for-each select="no_owner/application">
<tr>
<td><a href="/application/{@id}"><xsl:value-of select="name" /></a></td>
</tr>
</xsl:for-each>
</tbody>
</table>
</div>

<div class="col-sm-6">
<h2>Applications without hardware</h2>
<table class="table table-striped table-condensed no_hdw">
<thead>
<tr><th>Name</th></tr>
</thead>
<tbody>
<xsl:for-each select="no_hardware/application">
<tr>
<td><a href="/application/{@id}"><xsl:value-of select="name" /></a></td>
</tr>
</xsl:for-each>
</tbody>
</table>
</div>

</div>

<h2>Crowded servers</h2>
<table class="table table-striped table-condensed crowded">
<thead>
<tr><th>Name</th><th>Applications</th></tr>
</thead>
<tbody>
<xsl:for-each select="crowded/device">
<tr>
<td><a href="/hardware/{@id}"><xsl:value-of select="name" /></a></td>
<td><xsl:value-of select="applications" /></td>
</tr>
</xsl:for-each>
</tbody>
</table>

<div class="row">

<div class="col-sm-6">
<h2>Isolated business entities</h2>
<table class="table table-striped table-condensed iso_bus">
<thead>
<tr><th>Name</th></tr>
</thead>
<tbody>
<xsl:for-each select="isolated_business/business">
<tr>
<td><a href="/business/{@id}"><xsl:value-of select="name" /></a></td>
</tr>
</xsl:for-each>
</tbody>
</table>
</div>

<div class="col-sm-6">
<h2>Isolated hardware devices</h2>
<table class="table table-striped table-condensed iso_hdw">
<thead>
<tr><th>Name</th></tr>
</thead>
<tbody>
<xsl:for-each select="isolated_hardware/hardware">
<tr>
<td><a href="/hardware/{@id}"><xsl:value-of select="name" /></a></td>
</tr>
</xsl:for-each>
</tbody>
</table>
</div>

</div>
</xsl:template>

<!--
//
//  Content template
//
//-->
<xsl:template match="content">
<img src="/images/icons/issues.png" class="title_icon" />
<h1>Issues</h1>
<xsl:apply-templates select="overview" />
<xsl:apply-templates select="result" />
</xsl:template>

</xsl:stylesheet>
