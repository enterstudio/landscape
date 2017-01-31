<?xml version="1.0" ?>
<xsl:stylesheet version="1.1" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
<xsl:import href="banshee/main.xslt" />

<!--
//
//  Overview template
//
//-->
<xsl:template match="overview">
<div class="input-group filter">
<span class="input-group-addon" id="basic-addon1">Filter</span>
<input type="text" id="filter" class="form-control" onKeyUp="javascript:apply_filter()" />
<span class="input-group-btn"><button class="btn btn-default" type="button" onClick="javascript:clear_filter()">X</button></span>
</div>

<div class="row">
<div class="col-sm-4">
<img src="/images/icons/application.png" class="icon" />
<h2><a href="/application">Applications</a></h2>
<div id="applications" class="list-group">
<xsl:for-each select="applications/application">
<a href="/application/{@id}" class="list-group-item"><xsl:value-of select="name" /><span class="badge"><xsl:value-of select="links" /></span></a>
</xsl:for-each>
</div>
</div>
<div class="col-sm-4">
<img src="/images/icons/business.png" class="icon" />
<h2><a href="/business">Business</a></h2>
<div id="business" class="list-group">
<xsl:for-each select="business/item">
<a href="/business/{@id}" class="list-group-item"><xsl:value-of select="name" /><span class="badge"><xsl:value-of select="links" /></span></a>
</xsl:for-each>
</div>
</div>
<div class="col-sm-4">
<img src="/images/icons/hardware.png" class="icon" />
<h2><a href="/hardware">Hardware</a></h2>
<div id="hardware" class="list-group">
<xsl:for-each select="hardware/item">
<a href="/hardware/{@id}" class="list-group-item"><xsl:value-of select="name" /><span class="badge"><xsl:value-of select="links" /></span></a>
</xsl:for-each>
</div>
</div>
</div>

<div id="help">
<p>The number behind an object (application, business entity or hardware device) indicates the number of links (connection, used-by or runs-at) to that object.</p>
</div>
</xsl:template>

<!--
//
//  Content template
//
//-->
<xsl:template match="content">
<h1>Overview</h1>
<xsl:apply-templates select="overview" />
<xsl:apply-templates select="result" />
</xsl:template>

</xsl:stylesheet>
