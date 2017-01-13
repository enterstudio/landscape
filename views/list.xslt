<?xml version="1.0" ?>
<xsl:stylesheet version="1.1" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
<xsl:import href="banshee/main.xslt" />

<!--
//
//  Privacy template
//
//-->
<xsl:template match="privacy">
<table class="table table-condensed table-striped">
<thead>
<tr><th>Name</th><th>Owner</th><th>External</th></tr>
</thead>
<tbody>
<xsl:for-each select="record">
<tr>
<td><a href="/application/{@id}"><xsl:value-of select="name" /></a></td>
<td><a href="/business/{owner_id}"><xsl:value-of select="owner" /></a></td>
<td><xsl:value-of select="external" /></td>
</tr>
</xsl:for-each>
</tbody>
</table>
</xsl:template>

<!--
//
//  Protocols template
//
//-->
<xsl:template match="protocols">
<table class="table table-condensed table-striped">
<thead>
<tr><th>Protocol</th><th>From application</th><th>To application</th></tr>
</thead>
<tbody>
<xsl:for-each select="record">
<tr>
<td><xsl:value-of select="protocol" /></td>
<td><a href="/application/{@from_application_id}"><xsl:value-of select="from_app" /></a></td>
<td><a href="/application/{@to_application_id}"><xsl:value-of select="to_app" /></a></td>
</tr>
</xsl:for-each>
</tbody>
</table>
</xsl:template>

<!--
//
//  Input template
//
//-->
<xsl:template match="input">
<table class="table table-condensed table-striped">
<thead>
<tr><th>Input</th><th>At application</th><th>By business entity</th></tr>
</thead>
<tbody>
<xsl:for-each select="record">
<tr>
<td><xsl:value-of select="input" /></td>
<td><a href="/application/{app_id}"><xsl:value-of select="application" /></a></td>
<td><a href="/application/{bus_id}"><xsl:value-of select="business" /></a></td>
</tr>
</xsl:for-each>
</tbody>
</table>
</xsl:template>

<!--
//
//  List template
//
//-->
<xsl:template match="list">
<form action="/{/output/page}" method="post" class="types" onChange="javascript:submit()">
<select name="type" class="form-control">
<xsl:for-each select="types/option">
<option value="{@type}"><xsl:if test="@type=../@selected"><xsl:attribute name="selected">selected</xsl:attribute></xsl:if><xsl:value-of select="." /></option>
</xsl:for-each>
</select>
</form>

<xsl:apply-templates select="privacy" />
<xsl:apply-templates select="protocols" />
<xsl:apply-templates select="input" />
</xsl:template>

<!--
//
//  Content template
//
//-->
<xsl:template match="content">
<img src="/images/icons/list.png" class="title_icon" />
<h1>Lists</h1>
<xsl:apply-templates select="list" />
<xsl:apply-templates select="result" />
</xsl:template>

</xsl:stylesheet>
