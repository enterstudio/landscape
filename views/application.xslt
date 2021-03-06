<?xml version="1.0" ?>
<xsl:stylesheet version="1.1" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
<xsl:import href="banshee/main.xslt" />

<!--
//
//  Overview template
//
//-->
<xsl:template match="overview">
<table class="table table-striped table-condensed overview">
<thead>
<tr><th>Name</th><th>Type</th></tr>
</thead>
<tbody>
<xsl:for-each select="application">
<tr>
<td><a href="/{/output/page}/{@id}"><xsl:value-of select="name" /></a></td>
<td><xsl:value-of select="type" /></td>
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
//  Application template
//
//-->
<xsl:template match="application">
<xsl:if test="type!='' or description!=''">
<div class="panel panel-body panel-default">
<xsl:if test="type!=''"><div>Type: <xsl:value-of select="type" /></div></xsl:if>
<xsl:if test="description!=''"><div><xsl:value-of select="description" /></div></xsl:if>
</div>
</xsl:if>

<div class="row">
<div class="col-sm-4">Owner: <xsl:if test="owner!=''"><a href="/business/{owner_id}"><xsl:value-of select="owner" /></a></xsl:if></div>
<div class="col-sm-4">Location: <xsl:value-of select="location" /></div>
<div class="col-sm-4">Privacy law applicable: <xsl:value-of select="privacy_law" /></div>
</div>

<div class="row">
<div class="col-sm-4">Confidentiality: <xsl:value-of select="confidentiality" /></div>
<div class="col-sm-4">Integrity: <xsl:value-of select="integrity" /></div>
<div class="col-sm-4">Availability: <xsl:value-of select="availability" /></div>
</div>

<xsl:if test="labels/label">
<div class="labels">Labels:<xsl:for-each select="labels/label">
<xsl:if test="position()>1"><xsl:if test="@cid!=preceding::*[1]/@cid"><span>,</span></xsl:if></xsl:if>
<a href="/label/{@id}" class="label label-primary"><xsl:value-of select="." /></a>
</xsl:for-each></div>
</xsl:if>

<h2>Connections</h2>
<table class="table table-striped table-condensed table-xs connections">
<thead>
<tr><th>From</th><th>To</th><th>Protocol</th><th>Format</th><th>Frequency</th><th>Data flow</th><th></th></tr>
</thead>
<tbody>
<xsl:for-each select="connections/connection">
<tr>
<td><a href="/application/{from_application_id}"><xsl:value-of select="from_name" /></a></td>
<td><a href="/application/{to_application_id}"><xsl:value-of select="to_name" /></a></td>
<td><xsl:value-of select="protocol" /></td>
<td><xsl:value-of select="format" /></td>
<td><xsl:value-of select="frequency" /></td>
<td><xsl:value-of select="data_flow" /></td>
<td><xsl:if test="description!=''"><span class="glyphicon glyphicon-info-sign" onClick="javascript:show_dialog('con', {@id});" /></xsl:if></td>
</tr>
</xsl:for-each>
</tbody>
</table>

<div class="row">
<div class="col-sm-6">

<h2>Used by</h2>
<table class="table table-striped table-condensed usedby">
<thead>
<tr><th>Name</th><th>Information input</th><th></th></tr>
</thead>
<tbody>
<xsl:for-each select="used_by/entity">
<tr>
<td><a href="/business/{@id}"><xsl:value-of select="name" /></a></td>
<td><xsl:value-of select="input" /></td>
<td><xsl:if test="description!=''"><span class="glyphicon glyphicon-info-sign" onClick="javascript:show_dialog('use', {@id});" /></xsl:if></td>
</tr>
</xsl:for-each>
</tbody>
</table>

</div>
<div class="col-sm-6">

<h2>Runs at</h2>
<table class="table table-striped table-condensed runsat">
<thead>
<tr><th>Name</th><th>Operating System</th><th></th></tr>
</thead>
<tbody>
<xsl:for-each select="runs_at/device">
<tr>
<td><a href="/hardware/{@id}"><xsl:value-of select="name" /></a></td>
<td><a href="/hardware/{@id}"><xsl:value-of select="os" /></a></td>
<td><xsl:if test="description!=''"><span class="glyphicon glyphicon-info-sign" onClick="javascript:show_dialog('run', {@id});" /></xsl:if></td>
</tr>
</xsl:for-each>
</tbody>
</table>

</div>
</div>

<div class="btn-group">
<a href="/{@previous}" class="btn btn-default">Back</a>
</div>

<div class="dialogs">
<xsl:for-each select="connections/connection">
<xsl:if test="description!=''">
<div id="des_con_{@id}" title="{from_name} &#187; {to_name}"><span><xsl:value-of select="description" /></span></div>
</xsl:if>
</xsl:for-each>
<xsl:for-each select="used_by/entity">
<xsl:if test="description!=''">
<div id="des_use_{@id}" title="Used by {name}"><span><xsl:value-of select="description" /></span></div>
</xsl:if>
</xsl:for-each>
<xsl:for-each select="runs_at/device">
<xsl:if test="description!=''">
<div id="des_run_{@id}" title="Runs at {name}"><span><xsl:value-of select="description" /></span></div>
</xsl:if>
</xsl:for-each>
</div>
</xsl:template>

<!--
//
//  Content template
//
//-->
<xsl:template match="content">
<img src="/images/icons/application.png" class="title_icon" />
<h1><xsl:value-of select="/output/layout_site/title/@page" /></h1>
<xsl:apply-templates select="overview" />
<xsl:apply-templates select="application" />
<xsl:apply-templates select="result" />
</xsl:template>

</xsl:stylesheet>
