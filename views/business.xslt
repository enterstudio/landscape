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
<xsl:for-each select="entity">
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
//  Business template
//
//-->
<xsl:template match="business">
<xsl:variable name="col_use"><xsl:choose>
	<xsl:when test="count(usage/item)=0 or count(ownership/item)=0">12</xsl:when>
	<xsl:otherwise>8</xsl:otherwise>
</xsl:choose></xsl:variable>
<xsl:variable name="col_run"><xsl:choose>
	<xsl:when test="count(usage/item)=0 or count(ownership/item)=0">12</xsl:when>
	<xsl:otherwise>4</xsl:otherwise>
</xsl:choose></xsl:variable>
<xsl:if test="description!=''"><div class="panel panel-default panel-body"><xsl:value-of select="description" /></div></xsl:if>

<div class="row">
<div class="col-sm-{$col_use}">

<xsl:if test="count(usage/item)>0">
<h2>Application usage</h2>
<table class="table table-striped table-condensed usedby">
<thead>
<tr><th>Name</th><th>Information input</th><th></th></tr>
</thead>
<tbody>
<xsl:for-each select="usage/item">
<tr>
<td><a href="/application/{@id}"><xsl:value-of select="name" /></a></td>
<td><xsl:value-of select="input" /></td>
<td><xsl:if test="description!=''"><span class="glyphicon glyphicon-info-sign" onClick="javascript:show_dialog('use', {@id});" /></xsl:if></td>
</tr>
</xsl:for-each>
</tbody>
</table>
</xsl:if>

</div>
<div class="col-sm-{$col_run}">

<xsl:if test="count(ownership/item)>0">
<h2>Application ownership</h2>
<table class="table table-striped table-condensed ownage">
<thead>
<tr><th>Name</th></tr>
</thead>
<tbody>
<xsl:for-each select="ownership/item">
<tr><td><a href="/application/{@id}"><xsl:value-of select="name" /></a></td></tr>
</xsl:for-each>
</tbody>
</table>
</xsl:if>

</div>
</div>

<div class="btn-group">
<a href="/overview" class="btn btn-default">Back</a>
</div>

<xsl:if test="count(usage/item)>0">
<div class="dialogs">
<xsl:for-each select="usage/item">
<xsl:if test="description!=''">
<div id="des_use_{@id}" title="Used by {name}"><span><xsl:value-of select="description" /></span></div>
</xsl:if>
</xsl:for-each>
</div>
</xsl:if>
</xsl:template>

<!--
//
//  Content template
//
//-->
<xsl:template match="content">
<img src="/images/icons/business.png" class="title_icon" />
<h1><xsl:value-of select="/output/layout_site/title/@page" /></h1>
<xsl:apply-templates select="overview" />
<xsl:apply-templates select="business" />
<xsl:apply-templates select="result" />
</xsl:template>

</xsl:stylesheet>
