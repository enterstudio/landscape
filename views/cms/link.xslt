<?xml version="1.0" ?>
<xsl:stylesheet version="1.1" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
<xsl:import href="../banshee/main.xslt" />

<!--
//
//  Overview template
//
//-->
<xsl:template match="overview">
<form action="/{/output/page}" method="post">
<label for="application_id">Application:</label>
<select id="application_id" name="application_id" class="form-control" onChange="javascript:submit()">
<xsl:for-each select="applications/application">
<option value="{@id}"><xsl:if test="@id=../@selected"><xsl:attribute name="selected">selected</xsl:attribute></xsl:if><xsl:value-of select="name" /></option>
</xsl:for-each>
</select>
<input type="hidden" name="submit_button" value="set application" />
</form>

<h2>Connections</h2>
<table class="table table-hover table-striped table-condensed table-xs">
<thead>
<tr><th>From</th><th>To</th><th>Protocol</th><th>Format</th><th>Frequency</th><th>Data flow</th></tr>
</thead>
<tbody>
<xsl:for-each select="connections/connection">
<tr onClick="javascript:document.location='/{/output/page}/connection/{@id}'">
<td><xsl:value-of select="from_name" /></td>
<td><xsl:value-of select="to_name" /></td>
<td><xsl:value-of select="protocol" /></td>
<td><xsl:value-of select="format" /></td>
<td><xsl:value-of select="frequency" /></td>
<td><xsl:value-of select="data_flow" /></td>
</tr>
</xsl:for-each>
</tbody>
</table>

<div class="row">
<div class="col-sm-6">

<h2>Used by</h2>
<table class="table table-hover table-striped table-condensed">
<thead>
<tr><th>Name</th><th>Information input</th></tr>
</thead>
<tbody>
<xsl:for-each select="used_by/entity">
<tr onClick="javascript:document.location='/{/output/page}/usedby/{@id}'">
<td><xsl:value-of select="name" /></td>
<td><xsl:value-of select="input" /></td>
</tr>
</xsl:for-each>
</tbody>
</table>

</div>
<div class="col-sm-6">

<h2>Runs at</h2>
<table class="table table-hover table-striped table-condensed">
<thead>
<tr><th>Name</th><th>Operating System</th></tr>
</thead>
<tbody>
<xsl:for-each select="runs_at/device">
<tr onClick="javascript:document.location='/{/output/page}/runsat/{@id}'">
<td><xsl:value-of select="name" /></td>
<td><xsl:value-of select="os" /></td>
</tr>
</xsl:for-each>
</tbody>
</table>

</div>
</div>

<div class="btn-group">
<a href="/{/output/page}/connection" class="btn btn-default">New connection</a>
<a href="/{/output/page}/usedby" class="btn btn-default">New used-by</a>
<a href="/{/output/page}/runsat" class="btn btn-default">New runs-at</a>
<a href="/cms" class="btn btn-default">Back</a>
</div>
</xsl:template>

<!--
//
//  Connection template
//
//-->
<xsl:template match="connection">
<xsl:call-template name="show_messages" />
<form action="/{/output/page}" method="post">
<xsl:if test="@id"><input type="hidden" name="id" value="{@id}" /></xsl:if>
<input type="hidden" name="from_application_id" value="{from_application_id}" />
<label>From application:</label>
<p><xsl:value-of select="application" /></p>
<label for="to_application_id">To application:</label>
<select id="to_application_id" name="to_application_id" class="form-control">
<xsl:for-each select="applications/application">
<option value="{@id}"><xsl:if test="@id=../../to_application_id"><xsl:attribute name="selected">selected</xsl:attribute></xsl:if><xsl:value-of select="name" /></option>
</xsl:for-each>
</select>
<label for="protocol">Protocol:</label>
<input type="text" id="protocol" name="protocol" value="{protocol}" class="form-control" />
<label for="format">Format:</label>
<input type="text" id="format" name="format" value="{format}" class="form-control" />
<label for="frequency">Frequency:</label>
<input type="text" id="frequency" name="frequency" value="{frequency}" class="form-control" />
<label for="data_flow">Data flow:</label>
<select id="data_flow" name="data_flow" class="form-control">
<xsl:for-each select="data_flow/direction">
<option value="{@id}"><xsl:if test="@id=../../data_flow"><xsl:attribute name="selected">selected</xsl:attribute></xsl:if><xsl:value-of select="." /></option>
</xsl:for-each>
</select>
<label for="description">Description:</label>
<textarea id="description" name="description" class="form-control"><xsl:value-of select="description" /></textarea>

<div class="btn-group">
<input type="submit" name="submit_button" value="Save connection" class="btn btn-default" />
<a href="/{/output/page}" class="btn btn-default">Cancel</a>
<xsl:if test="@id">
<input type="submit" name="submit_button" value="Delete connection" class="btn btn-default" onClick="javascript:return confirm('DELETE: Are you sure?')" />
</xsl:if>
</div>
</form>

<div id="help">
<ul>
<li><b>From application:</b> The application that initiates the connection.</li>
<li><b>To application:</b> The application to which the connection is made.</li>
<li><b>Protocol:</b> The protocol that is used to transport the information, e.g. HTTP</li>
<li><b>Format:</b> The format in which the information is transported, e.g. XML.</li>
<li><b>Frequency:</b> How often the information is being send.</li>
<li><b>Data flow:</b> The direction in which the information flows.</li>
</ul>
</div>
</xsl:template>

<!--
//
//  Used by template
//
//-->
<xsl:template match="usedby">
<xsl:call-template name="show_messages" />
<form action="/{/output/page}" method="post">
<xsl:if test="@id"><input type="hidden" name="id" value="{@id}" /></xsl:if>
<input type="hidden" name="application_id" value="{application_id}" />
<label>Application:</label>
<p><xsl:value-of select="application" /></p>
<label for="business_id">Used by:</label>
<select id="business_id" name="business_id" class="form-control">
<xsl:for-each select="business/entity">
<option value="{@id}"><xsl:if test="@id=../../business_id"><xsl:attribute name="selected">selected</xsl:attribute></xsl:if><xsl:value-of select="name" /></option>
</xsl:for-each>
</select>
<label for="input">Information input:</label>
<input type="text" id="input" name="input" value="{input}" class="form-control" />
<label for="description">Description:</label>
<textarea id="description" name="description" class="form-control"><xsl:value-of select="description" /></textarea>

<div class="btn-group">
<input type="submit" name="submit_button" value="Save used-by" class="btn btn-default" />
<a href="/{/output/page}" class="btn btn-default">Cancel</a>
<xsl:if test="@id">
<input type="submit" name="submit_button" value="Delete used-by" class="btn btn-default" onClick="javascript:return confirm('DELETE: Are you sure?')" />
</xsl:if>
</div>
</form>

<div id="help">
<ul>
<li><b>Information input:</b> Description of manually entered information.</li>
</ul>
</div>
</xsl:template>

<!--
//
//  Runs at template
//
//-->
<xsl:template match="runsat">
<xsl:call-template name="show_messages" />
<form action="/{/output/page}" method="post">
<xsl:if test="@id"><input type="hidden" name="id" value="{@id}" /></xsl:if>
<input type="hidden" name="application_id" value="{application_id}" />
<label>Application:</label>
<p><xsl:value-of select="application" /></p>
<label for="hardware_id">Runs at:</label>
<select id="hardware_id" name="hardware_id" class="form-control">
<xsl:for-each select="hardware/device">
<option value="{@id}"><xsl:if test="@id=../../hardware_id"><xsl:attribute name="selected">selected</xsl:attribute></xsl:if><xsl:value-of select="name" /></option>
</xsl:for-each>
</select>

<div class="btn-group">
<input type="submit" name="submit_button" value="Save runs-at" class="btn btn-default" />
<a href="/{/output/page}" class="btn btn-default">Cancel</a>
<xsl:if test="@id">
<input type="submit" name="submit_button" value="Delete runs-at" class="btn btn-default" onClick="javascript:return confirm('DELETE: Are you sure?')" />
</xsl:if>
</div>
</form>
</xsl:template>

<!--
//
//  Content template
//
//-->
<xsl:template match="content">
<img src="/images/icons/links.png" class="title_icon" />
<h1>Link administration</h1>
<xsl:apply-templates select="overview" />
<xsl:apply-templates select="connection" />
<xsl:apply-templates select="usedby" />
<xsl:apply-templates select="runsat" />
<xsl:apply-templates select="result" />
</xsl:template>

</xsl:stylesheet>
