<?xml version="1.0" ?>
<xsl:stylesheet version="1.1" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
<xsl:import href="main.xslt" />

<!--
//
//  Login template
//
//-->
<xsl:template match="login">
<xsl:call-template name="show_messages" />
<form id="login" action="{url}" method="post" autocomplete="off">
<label for="username">Username:</label>
<input type="text" autocapitalize="off" autocorrect="off" id="username" name="username" value="{username}" class="form-control" />
<label for="password">Password:</label>
<input type="password" id="password" name="password" class="form-control" />
<xsl:if test="@authenticator='yes'">
<label for="code">Authenticator code:</label>
<input type="text" id="code" name="code" class="form-control" />
</xsl:if>
<p>Bind session to IP (<xsl:value-of select="remote_addr" />): <input type="checkbox" name="bind_ip">
<xsl:if test="@bind_ip='yes'">
<xsl:attribute name="checked">checked</xsl:attribute>
</xsl:if>
</input></p>
<div class="btn-group">
<input type="submit" value="Login" class="btn btn-default" />
</div>
</form>

<xsl:if test="@password='yes'"><p>If you have forgotten your password, click <a href="/password">here</a>.</p></xsl:if>
<xsl:if test="@register='yes'"><p>Click <a href="/register">here</a> to register for an account.</p></xsl:if>
</xsl:template>

<!--
//
//  Content template
//
//-->
<xsl:template match="content">
<h1>Login</h1>
<xsl:apply-templates select="login" />
<xsl:apply-templates select="result" />
</xsl:template>

</xsl:stylesheet>
